@if($user)
    <h2>User info</h2>
    @if($user->hasRole('admin'))
        <h3>Admin</h3>
    @endif
    <form class="form">
        <label>Name</label><input class="form-control" type="text" readonly
                                  value="{{ $user->first_name }} {{ $user->last_name }}">
        <label>Email</label><input class="form-control" type="text" readonly value="{{ $user->email }}">
    </form>
    <p>User since {{ $user->created_at }}</p>
@else
    @include('auth::login')
@endif