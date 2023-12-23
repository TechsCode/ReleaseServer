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
            <div class="success-message">
                <div class="success-message-icon">
                    <x-icon-circle-check />
                </div>
                <div class="success-message-text">
                    <span class="title">Update Successful</span>
                    <span class="message">You May now close this window</span>
                </div>
            </div>
        @endif
    </div>
@endsection
