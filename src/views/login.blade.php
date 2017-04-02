<h2>Sign up/Login with</h2>
<p>
<div class="btn-group btn-group-horizontal">
    <a href="{{ route('auth.redirect', ['provider' => 'facebook']) }}" class="btn btn-lg btn-primary facebook"
       type="submit"><i class="fa fa-facebook" aria-hidden="true"></i> Facebook</a>
    <a href="{{ route('auth.redirect', ['provider' => 'google']) }}" class="btn btn-lg btn-primary google"
       type="submit"><i class="fa fa-google" aria-hidden="true"></i> Google</a>
</div>
</p>
<a class="btn" href="{{ route('terms_path') }}">Terms of service</a> <a class="btn" href="{{ route('privacy_path') }}">Privacy policy</a>