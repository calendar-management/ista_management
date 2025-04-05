<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <title>Login</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <style>
            :root {
                --primary-color: #3b82f6;
                --primary-hover: #2563eb;
                --bg-color: #f5f7fa;
                --text-color: #374151;
                --border-color: #e5e7eb;
                --error-color: #ef4444;
            }
            
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Figtree', sans-serif;
                background-color: var(--bg-color);
                color: var(--text-color);
                line-height: 1.5;
            }
            
            .page-container {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 2rem 1rem;
            }
            
            .logo {
                width: 80px;
                height: 80px;
                margin-bottom: 1.5rem;
            }
            
            .form-container {
                width: 100%;
                max-width: 400px;
                background-color: white;
                border-radius: 0.75rem;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                padding: 2rem;
            }
            
            .form-title {
                font-size: 1.5rem;
                font-weight: 600;
                text-align: center;
                margin-bottom: 1.5rem;
                color: #1f2937;
            }
            
            .form-group {
                margin-bottom: 1.25rem;
            }
            
            .form-label {
                display: block;
                font-size: 0.875rem;
                font-weight: 500;
                margin-bottom: 0.5rem;
            }
            
            .form-input {
                width: 100%;
                padding: 0.75rem 1rem;
                border: 1px solid var(--border-color);
                border-radius: 0.5rem;
                font-size: 1rem;
                transition: border-color 0.2s, box-shadow 0.2s;
            }
            
            .form-input:focus {
                outline: none;
                border-color: var(--primary-color);
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            }
            
            .error-message {
                color: var(--error-color);
                font-size: 0.875rem;
                margin-top: 0.5rem;
            }
            
            .checkbox-container {
                display: flex;
                align-items: center;
                margin-bottom: 1.5rem;
            }
            
            .checkbox-input {
                width: 1rem;
                height: 1rem;
                border-radius: 0.25rem;
                border: 1px solid var(--border-color);
                margin-right: 0.5rem;
            }
            
            .checkbox-label {
                font-size: 0.875rem;
                color: #6b7280;
            }
            
            .form-button {
                background-color: var(--primary-color);
                color: white;
                border: none;
                border-radius: 0.5rem;
                padding: 0.75rem 1.5rem;
                font-size: 0.875rem;
                font-weight: 500;
                cursor: pointer;
                width: 100%;
                transition: background-color 0.2s;
            }
            
            .form-button:hover {
                background-color: var(--primary-hover);
            }
            
            @media (max-width: 640px) {
                .form-container {
                    padding: 1.5rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="page-container">
            <div class="logo">
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>
            
            <div class="form-container">
                <!-- Session Status (placeholder) -->
                <div id="session-status" class="error-message" style="display: none; margin-bottom: 1rem;"></div>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <h1 class="form-title">Se connecter à votre compte</h1>
                    
                    <!-- Email/Login -->
                    <div class="form-group">
                        <label for="email" class="form-label">Login</label>
                        <input id="email" class="form-input" type="text" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                        @if($errors->get('email'))
                        <div class="error-message">{{ $errors->first('email') }}</div>
                        @endif
                    </div>
                    
                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input id="password" class="form-input" type="password" name="password" required autocomplete="current-password">
                        @if($errors->get('password'))
                        <div class="error-message">{{ $errors->first('password') }}</div>
                        @endif
                    </div>
                    
                    <!-- Remember Me -->
                    <div class="checkbox-container">
                        <input id="remember_me" type="checkbox" class="checkbox-input" name="remember">
                        <label for="remember_me" class="checkbox-label">Se souvenir de moi</label>
                    </div>
                    
                    <button type="submit" class="form-button">
                        Log in
                    </button>
                </form>
            </div>
        </div>
    </body>
</html>