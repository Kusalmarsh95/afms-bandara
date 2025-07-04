@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Update Role</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('roles.index') }}" class="btn btn-sm btn-dark">Back</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <script type="text/javascript">
                $(document).ready(function () {
                    setTimeout(function () {
                        $('.alert').fadeOut();
                    }, 2000);
                });
            </script>
        @endif
        <div class="card">
            <div class="card-header">
                <div class="container-fluid">
                    <form action="{{ route('roles.update',$role->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-header">
                            <div class="card-title">Role Details</div>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="name" class="col-sm-4 col-form-label">Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="name" class="form-control" value="{{$role->name}}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12 row">
                                    <label for="category_id" class="col-2 col-form-label">Permissions</label>
                                    <div class="col-10 col-form-label">
                                        @foreach($moduleWisePermissions as $moduleName => $permissions)
                                        <div class="col-10 col-form-label">
                                            <div>
                                                @php
                                                foreach($permissions as $permissionId => $permissionName)
                                                    if ($permissionName == $moduleName) {
                                                        $checkboxId = "permission".$permissionId;
                                                        $checkboxValue = $permissionId;
                                                        $permissionIdOfModule = $permissionId;
                                                    }
                                                @endphp
                                                <input id="{{ $checkboxId }}" type="checkbox" name="permission[]" value="{{ $checkboxValue }}" class="form-check-input module" {{ in_array($permissionIdOfModule, $rolePermissions) ? 'checked' : '' }}>
                                                <h5 for="{{ $checkboxId }}">{{ ucwords(str_replace('-', ' ', $moduleName)) }}</h5>
                                            </div>
                                            <div id="{{ $permissionIdOfModule }}">
                                                @foreach($permissions as $permissionId => $permissionName)
                                                @if($permissionName !== $moduleName)
                                                <div class="form-check">
                                                    <input data-module-permission-id="{{ $checkboxId }}" type="checkbox" name="permission[]" value="{{ $permissionId }}" class="form-check-input module-permission" id="permission{{ $permissionId }}" {{ in_array($permissionId, $rolePermissions) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="permission{{ $permissionId }}">
                                                        {{ ucwords(trim(str_replace('-', ' ', str_replace($moduleName, '', $permissionName)))) }}
                                                    </label>
                                                </div>
                                                @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-center">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>

        //Check all/uncheck all when module name checked
        var modules = document.getElementsByClassName('module'); // all module checkboxes
        for (var i=0; i<modules.length; i++) {
            modules[i].onchange = function () {
                var id = this.getAttribute('value');
                if (this.checked) selectAll(id);
                if (!this.checked) unselectAll(id);
            }
        }

        //Auto check module name when a permission is checked
        var modulePermissions = document.getElementsByClassName('module-permission'); // all module checkboxes
        for (var i=0; i<modulePermissions.length; i++) {
            modulePermissions[i].onchange = function () {
                var id = this.getAttribute('data-module-permission-id');
                if (this.checked) {
                    document.getElementById(id).checked = true;
                }
            }
        }

        function selectAll(id) {
            var checkboxes = document.getElementById(id).querySelectorAll('input[type="checkbox"]');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = true;
            }
        }

        function unselectAll(id){
            var checkboxes = document.getElementById(id).querySelectorAll('input[type="checkbox"]');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = false;
            } 
        }  

    </script>
@endsection


