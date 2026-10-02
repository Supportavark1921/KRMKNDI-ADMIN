{{-- Permission matrix partial. Expects: $permissionMap, $allPerms, $rolePerms (keyed by perm name) --}}
<div class="store-card" style="overflow-x:auto">
    <table class="store-table" style="min-width:600px">
        <thead>
            <tr>
                <th style="width:200px">Menu</th>
                <th>View</th><th>Create</th><th>Update</th><th>Delete</th><th>Restore</th>
            </tr>
        </thead>
        <tbody>
        @foreach($permissionMap as $menu => $actions)
        <tr>
            <td><strong>{{ $menu }}</strong></td>
            @foreach(['view','create','update','delete','restore'] as $action)
            <td style="text-align:center">
                @php $perm = "{$menu}.{$action}"; @endphp
                @if(in_array($action, $actions) && isset($allPerms[$perm]))
                <input type="checkbox" name="permissions[{{ $perm }}]" value="1"
                    {{ isset($rolePerms[$perm]) ? 'checked' : '' }}>
                @else
                <span style="color:#ccc">—</span>
                @endif
            </td>
            @endforeach
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
