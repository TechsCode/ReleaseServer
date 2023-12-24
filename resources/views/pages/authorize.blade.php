@extends('layouts.app')

@section('content')
    <div class="auth-box-container">
        <div class="auth-box-title">
            TechsCode Updater
        </div>

        <div class="auth-box-screen-to-small">
            <div class="error-message">
                <div class="error-message-icon">
                    <x-icon-circle-x />
                </div>
                <div class="error-message-text">
                    <span class="title">Screen Too Small</span>
                    <span class="message">Your screen is too small to use the updater.</span>
                </div>
            </div>
        </div>

        @if(!config('services.update_server_enabled'))
            <div class="error-message">
                <div class="error-message-icon">
                    <x-icon-circle-x />
                </div>
                <div class="error-message-text">
                    <span class="title">Unavailable</span>
                    <span class="message">The update server is currently unavailable. <br>Please try again later.</span>
                </div>
            </div>
        @else
            @if(isset($error_message))
                <div class="error-message">
                    <div class="error-message-icon">
                        <x-icon-circle-x />
                    </div>
                    <div class="error-message-text">
                        <span class="title">{{ $error_title }}</span>
                        <span class="message">{{ $error_message }}</span>
                    </div>
                </div>
            @else
                <div class="auth-box">
                    <span class="info-text">You are about to update the plugin:</span>
                    <span class="plugin-name">{{ $plugin_name }}</span>
                    <span class="from-text">Updating From:</span>
                    <div class="current-version">
                        {{$current_version}} @if($current_version_date) ({{ \Illuminate\Support\Carbon::make($current_version_date)->format("d/m/Y") }}) @endif
                    </div>
                    <span class="to-text">
                    <x-icon-arrow-down />
                </span>
                    <div class="update-to">
                        @php
                            $split = explode("_", $update_to);
                            $version = $split[0];
                            echo $version . " (Latest)";
                        @endphp
                    </div>

                    <span class="auth-text">Authenticate with discord to proceed with the plugin update!</span>

                    <div class="discord-button" onclick="window.location.href = `{{ route('authenticate', $update_token) }}`">
                        <x-icon-discord />
                        Continue with Discord
                    </div>
                </div>
            @endif
        @endif
    </div>
@endsection
