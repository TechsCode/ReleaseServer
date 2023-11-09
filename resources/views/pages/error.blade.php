@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-auto w-100 d-flex justify-content-center">
            <div class="updated-container">
                <div class="icon-container x-mark">
                    <i class="fa-regular fa-circle-xmark"></i>
                </div>
                <span class="updated-title">Update Failed</span>
                <span class="updated-message text-center">{!! $error_message !!}</span>

                @if(isset($show_join_button))
                    <div class="btn mt-4 btn-techscode" onclick="window.location.href = `https://discord.techscode.com`">
                        Join the Support Server
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
