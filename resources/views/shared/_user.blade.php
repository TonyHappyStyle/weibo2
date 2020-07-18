<div class="list-group-item">
    <img class="mr-3" src="{{ $user->gravatar() }}" alt="{{ $user->name }}" width=32>
    <a href="{{ route('users.show', $user) }}">
        {{ $user->name }}
    </a>
    @can('destroy',$user)
        <form method="post" action="{{ route('users.destroy',$user) }}" class="float-right" onsubmit=" return confirm('确定要删除{{ $user->name }}？');">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <button type="submit" class="btn btn-sm btn-danger delete-btn">删除</button>
        </form>
    @endcan
</div>
