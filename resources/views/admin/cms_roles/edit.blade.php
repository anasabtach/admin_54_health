@extends('admin.master')
@section('content')
    <section class="main-content">
        <div class="row">
            <div class="col-sm-12">
                @include('admin.flash-message')
                <div class="card">
                    <div class="card-header card-default">
                        <div class="row">
                            <div class="col-md-6">
                                Edit Cms Role
                            </div>
                            <div class="col-md-6">
                                <a class="btn btn-info pull-right" href="{{ route('cms-roles-management.create') }}">Add New Cms Role</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('cms-roles-management.update',[ 'cms_roles_management' => $record->slug ]) }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="PUT">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" value="{{ $record->title }}" name="name" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Is Super Admin</label>
                                        <select class="form-control" id="is_super_admin" name="is_super_admin" required>
                                            <option value="0" {{ $record->is_super_admin == '0' ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ $record->is_super_admin == '1' ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="module_permission_section">
                                <div class="col-md-12">
                                    <h3 class="margin-tb-20">Module Permissions</h3>
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>modules</th>
                                            <th class="text-center" width="10%">View</th>
                                            <th class="text-center" width="10%">Add</th>
                                            <th class="text-center" width="10%">Update</th>
                                            <th class="text-center" width="10%">Delete</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td class="text-center">
                                                <input type="checkbox" class="check_all" name="view_all" value="view">
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" class="check_all" name="add_all" value="add">
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" class="check_all" name="update_all" value="update">
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" class="check_all" name="delete_all" value="delete">
                                            </td>
                                        </tr>
                                        @if( count($getModules) )
                                            @foreach( $getModules as $modules )
                                                @if( count($modules->child) )
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td colspan="5">{{ $modules->name }}</td>
                                                    </tr>
                                                    @foreach( $modules->child as $childModules )
                                                        <tr>
                                                            <td>
                                                                <input type="hidden" name="module_id[]" value="{{ $childModules->id }}">
                                                                <span class="ml-3">{{ $loop->parent->iteration . '.' . $loop->iteration }}</span>
                                                            </td>
                                                            <td><span class="ml-3">{{ $childModules->name }}</span></td>
                                                            <td class="text-center">
                                                                <input type="checkbox" class="view_checkbox" name="is_view[{{ $childModules->id }}]" value="1" {{ !empty($childModules->is_view) ? 'checked' : '' }}>
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox" class="add_checkbox" name="is_add[{{ $childModules->id }}]" value="1" {{ !empty($childModules->is_add) ? 'checked' : '' }}>
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox" class="update_checkbox" name="is_update[{{ $childModules->id }}]" value="1" {{ !empty($childModules->is_update) ? 'checked' : '' }}>
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox" class="delete_checkbox" name="is_delete[{{ $childModules->id }}]" value="1" {{ !empty($childModules->is_delete) ? 'checked' : '' }}>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td>
                                                            <input type="hidden" name="module_id[]" value="{{ $modules->id }}">
                                                            {{ $loop->iteration }}
                                                        </td>
                                                        <td>{{ $modules->name }}</td>
                                                        <td class="text-center">
                                                            <input type="checkbox" class="view_checkbox" name="is_view[{{ $modules->id }}]" value="1" {{ !empty($modules->is_view) ? 'checked' : '' }}>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="checkbox" class="add_checkbox" name="is_add[{{ $modules->id }}]" value="1" {{ !empty($modules->is_add) ? 'checked' : '' }}>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="checkbox" class="update_checkbox" name="is_update[{{ $modules->id }}]" value="1" {{ !empty($modules->is_update) ? 'checked' : '' }}>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="checkbox" class="delete_checkbox" name="is_delete[{{ $modules->id }}]" value="1" {{ !empty($modules->is_delete) ? 'checked' : '' }}>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.footer')
    </section>
@endsection
