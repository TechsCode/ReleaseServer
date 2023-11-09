@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-auto d-flex justify-content-center w-100">

            <div class="card" style="width: 18rem;">
                <div class="card-header text-center">
                    <h2>Update Server</h2>
                </div>
                <div class="card-body text-center">
                    @if(!empty($error_message))
                        <div class="alert alert-danger">
                            <div class="alert-title">Error!</div>
                            <div class="alert-body">
                                {{$error_message}}
                            </div>
                        </div>
                    @endif

                    @if($show_auth_button)
                        <div class="update-info">
                            @if($plugin_name && $current_version && $update_to)
                                <p>
                                    You are about to update<br>
                                    <strong>{{$plugin_name}}</strong><br>
                                    from version<br>
                                    <span class="current-version">{{$current_version}}</span> to <span class="update-to">{{$update_to}}</span>!
                                </p>
                            @else
                                <p>
                                    You are about to authenticate the updater gui.
                                </p>
                            @endif
                        </div>
                        <div class="confirm-text">
                            Click the button below to authenticate the update.
                        </div>
                        <div class="btn w-100 btn-techscode" onclick="window.location.href = `{{ route('authenticate', $update_token) }}`">
                            Authenticate Update<br>
                            With Discord
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
