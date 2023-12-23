@extends('layouts.app')

@section('content')
    <div class="auth-box-container">

        <div class="auth-box-title">
            TechsCode Updater
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
            <div class="error-message">
                <div class="error-message-icon">
                    <x-icon-circle-x />
                </div>
                <div class="error-message-text">
                    <span class="title">Update Failed</span>
                    <span class="message">{!! $error_message !!}</span>
                </div>
            </div>

            @if($show_join_button)
                <div class="join-discord-btn">
                    <div class="discord-button" onclick="window.location.href = `https://discord.techscode.com`">
                        <x-icon-discord />
                        Join our Discord
                    </div>
                </div>
            @endif
        @endif
    </div>
@endsection
