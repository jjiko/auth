@if($user)
    <h2>User info</h2>
    @if($user->hasRole('admin'))
        <h3>Admin</h3>
    @endif
    @if($TSUser = $user->TSUser)
        <h3>Teamspeak</h3>
        {{ $TSUser->nickname }}
    @endif
    <form class="form">
        <label>Name</label><input class="form-control" type="text" readonly
                                  value="{{ $user->first_name }} {{ $user->last_name }}">
        <label>Email</label><input class="form-control" type="text" readonly value="{{ $user->email }}">
    </form>
    <p>User since {{ $user->created_at }}</p>
    <div class="row">
        <div class="col-md-3">
            <h2>Google</h2>
            @if($gUser = \Jiko\Auth\OAuthUser::where('user_id', $user->id)->where('provider', 'google')->first())
                <p>Connected on {{ $gUser->created_at }}</p>
            @else
                <a href="{{ route('auth.redirect', ['provider' => 'google']) }}"
                   class="btn btn-lg btn-primary"
                   type="submit">connect</a>
            @endif
        </div>
        <div class="col-md-3">
            <h2>Facebook</h2>
            @if($fbUser = \Jiko\Auth\OAuthUser::where('user_id', $user->id)->where('provider', 'facebook')->first())
                <p>Connected on {{ $fbUser->created_at }}</p>
            @else
                <a href="{{ route('auth.redirect', ['provider' => 'facebook']) }}"
                   class="btn btn-lg btn-primary"
                   type="submit">connect</a>
            @endif
        </div>
        <div class="col-md-3">
            <h2>Twitter</h2>
            @if($twitterUser = \Jiko\Auth\OAuthUser::where('user_id', $user->id)->where('provider', 'twitter')->first())
                <p>Connected on {{ $twitterUser->created_at }}</p>
            @else
                <a href="{{ route('auth.connect.redirect', ['provider' => 'twitter']) }}"
                   class="btn btn-lg btn-primary"
                   type="submit">connect</a>
            @endif
        </div>
        <div class="col-md-3">
            <h2>Instagram</h2>
            @if($igUser = \Jiko\Auth\OAuthUser::where('user_id', $user->id)->where('provider', 'instagram')->first())
                <p>Connected on {{ $igUser->created_at }}</p>
            @else
                <a href="{{ route('auth.connect.redirect', ['provider' => 'instagram']) }}"
                   class="btn btn-lg btn-primary"
                   type="submit">connect</a>
            @endif
        </div>
        <div class="col-md-3">
            <h2>Twitch.tv</h2>
            @if($twitchUser = \Jiko\Auth\OAuthUser::where('user_id', $user->id)->where('provider', 'twitch')->first())
                <p>Connected on {{ $twitchUser->created_at }}</p>
            @else
                <a href="{{ route('auth.connect.redirect', ['provider' => 'twitch']) }}"
                   class="btn btn-lg btn-primary"
                   type="submit">connect</a>
            @endif
        </div>
        <div class="col-md-3">
            <h2>Spotify</h2>
            @if($spotifyUser = \Jiko\Auth\OAuthUser::where('user_id', $user->id)->where('provider', 'spotify')->first())
                <p>Connected on {{ $spotifyUser->created_at }}</p>
            @else
                <a href="{{ route('auth.connect.redirect', ['provider' => 'spotify']) }}"
                   class="btn btn-lg btn-primary"
                   type="submit">connect</a>
            @endif
        </div>
        <div class="col-md-3">
            <h2>Steam</h2>
            @if($steamUser = \Jiko\Auth\OAuthUser::where('user_id', $user->id)->where('provider', 'steam')->first())
                <p>Connected on {{ $steamUser->created_at }}</p>
            @else
                <a href="{{ route('auth.connect.redirect', ['provider' => 'steam']) }}"
                   class="btn btn-lg btn-primary"
                   type="submit"><img
                            src="https://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_01.png"
                            alt="Steam"></a>
            @endif
        </div>

        <div class="col-md-3">
            <h2>BlueIris</h2>
            @if($BIUser = \Jiko\Auth\OAuthUser::where('user_id', $user->id)->where('provider', 'blueiris')->first())
                <p>Connected on {{ $BIUser->created_at }}</p>
            @else
                <a href="{{ route('auth.connect.redirect', ['provider' => 'blueiris']) }}"
                   class="btn btn-lg btn-primary"
                   type="submit">configure</a>
            @endif
        </div>
        @if($psnUser = \Jiko\Auth\OAuthUser::where('user_id', $user->id)->where('provider', 'playstation')->first())
            <div class="col-md-3">
                <h2>Playstation Network</h2>
                <p>Connected on {{ $psnUser->created_at }}</p>
            </div>
        @endif
    </div>
@else
    @include('auth::login')
@endif