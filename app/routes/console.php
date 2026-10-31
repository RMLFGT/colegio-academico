<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('academic:create-admin', function () {
    if (\App\Models\User::where('role', 'admin')->exists()) {
        $this->error('Ya existe una cuenta administradora.');
        return 1;
    }
    $name = $this->ask('Nombre');
    $username = $this->ask('Nombre de usuario');
    $email = $this->ask('Correo');
    $password = $this->secret('Contraseña (mínimo 12 caracteres)');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password ?? '') < 12 || !$name || !$username) {
        $this->error('Datos inválidos.');
        return 1;
    }
    \App\Models\User::create(['name' => $name, 'username' => $username, 'email' => $email, 'password' => $password, 'role' => 'admin', 'active' => true]);
    $this->info('Cuenta administradora creada.');
})->purpose('Create the initial administrator interactively');

Artisan::command('academic:prepare-presentation', function () {
    if (!config('academic.presentation_data')) {
        $this->info('Carga de presentación desactivada.');
        return 0;
    }
    $username = config('academic.admin_username');
    $email = config('academic.admin_email');
    $password = config('academic.admin_password');
    $validator = \Illuminate\Support\Facades\Validator::make(
        compact('username', 'email', 'password'),
        ['username' => 'required|string|max:255', 'email' => 'required|email', 'password' => 'required|string|min:12']
    );
    if ($validator->fails()) {
        $this->error('Configure ADMIN_USERNAME, ADMIN_EMAIL y ADMIN_PASSWORD (mínimo 12 caracteres).');
        return 1;
    }
    // Presentation data and the first admin are committed together, never reset.
    \Illuminate\Support\Facades\DB::transaction(function () use ($username, $email, $password) {
        $existing = \App\Models\User::where('username', $username)->first();
        if ($existing && ($existing->role !== 'admin' || !$existing->active)) {
            throw new \RuntimeException('El nombre administrativo ya pertenece a otra cuenta.');
        }
        if (!$existing && !\App\Models\User::where('role', 'admin')->exists()) {
            \App\Models\User::create([
                'name' => 'Administración del colegio', 'username' => $username,
                'email' => $email, 'password' => $password, 'role' => 'admin', 'active' => true,
            ]);
        }
        $result = $this->call('db:seed', ['--class' => 'AcademicDataSeeder', '--force' => true]);
        if ($result !== 0) {
            throw new \RuntimeException('No se pudieron preparar los datos de presentación.');
        }
    });
    $this->info('Presentación preparada. Las contraseñas existentes se conservan.');
    return 0;
})->purpose('Preparar datos ficticios y el primer administrador en el alojamiento de presentación');
