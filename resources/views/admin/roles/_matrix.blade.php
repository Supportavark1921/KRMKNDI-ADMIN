{{-- Permission matrix partial. Expects: $permissionMap, $allPerms, $rolePerms (keyed by perm name) --}}
<table class="rol-matrix-table">
    <thead>
        <tr>
            <th style="width:200px;text-align:left">Menu</th>
            <th>View</th>
            <th>Create</th>
            <th>Update</th>
            <th>Delete</th>
            <th>Restore</th>
        </tr>
    </thead>
    <tbody>
    @foreach($permissionMap as $menu => $actions)
    <tr>
        <td><span class="rol-menu-name">{{ $menu }}</span></td>
        @foreach(['view','create','update','delete','restore'] as $action)
        <td>
            @php $perm = "{$menu}.{$action}"; @endphp
            @if(in_array($action, $actions) && isset($allPerms[$perm]))
            <div class="rol-cb-wrap">
                <input type="checkbox" name="permissions[{{ $perm }}]" value="1"
                    class="rol-cb"
                    {{ isset($rolePerms[$perm]) ? 'checked' : '' }}>
            </div>
            @else
            <span class="rol-dash">—</span>
            @endif
        </td>
        @endforeach
    </tr>
    @endforeach
    </tbody>
</table>
