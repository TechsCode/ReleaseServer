@extends('layouts.app')

@section('content')
    <div class="row justify-content-center gap-5">
        <div class="col-auto">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h1 class="card-title">TechsCode Updater</h1>
                    <p class="card-text">The TechsCode update server allows you to effortlessly update your plugins.</p>
                </div>
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
