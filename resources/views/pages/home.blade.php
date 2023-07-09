@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-auto">
            <div class="card">
                <h1>TechsCode Updater</h1>
                <br>
                <p>
                    The TechsCode update server allows you to effortlessly update your plugins.
                </p>
            </div>
        </div>
        <div class="col-auto">
            <div class="image-stack">
                <img src="{{ Storage::url('images/discord_browser.png') }}" alt="" class="browser-img">
                <img src="{{ Storage::url('images/update_inventory.png') }}" alt="" class="inventory-img">
            </div>
        </div>
    </div>
@endsection
