<div id="chat-tables">
    <!-- User-Initiated Conversations -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-bd lobidrag">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-comment-dots"></i> 
                        {{ __('messages.conversations_started_by_users') }}
                        <small class="text-muted">({{ __('messages.users_who_contacted_you') }})</small>
                    </h4>
                </div>
                <div class="panel-body">
                    <!-- Subscribers who contacted admin -->
                    @if($userInitiatedSubscribers->count() > 0)
                    <h5><i class="fa fa-star text-warning"></i> {{ __('messages.subscribers') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.user') }}</th>
                                    <th>{{ __('messages.message') }}</th>
                                    <th>{{ __('messages.image') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th class="no-print">{{ __('messages.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userInitiatedSubscribers as $message)
                                <tr @if(!$message->is_read) style="background:#f9f9f9" @endif>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $message->counterparty->f_name ?? 'Unknown User' }}</strong>
                                        <br><small class="text-muted">{{ $message->counterparty->email }}</small>
                                    </td>
                                    <td>{{ Str::limit($message->message, 50) }}</td>
                                    <td>
                                        @if(!empty($message->image_path))
                                            <img src="{{ asset('storage/' . $message->image_path) }}" alt="image" style="max-height:40px;max-width:60px" />
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($message->is_read)
                                        <span class="badge badge-success">{{ __('messages.read') }}</span>
                                        @else
                                        <span class="badge badge-warning">{{ __('messages.unread') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $message->created_at->format('d M Y h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('admin.chat.show', $message->counterparty->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fa fa-eye"></i> {{ __('messages.open') }}
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    <!-- Simple Users who contacted admin -->
                    @if($userInitiatedSimpleUsers->count() > 0)
                    <h5><i class="fa fa-user text-info"></i> {{ __('messages.simple_users') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.user') }}</th>
                                    <th>{{ __('messages.message') }}</th>
                                    <th>{{ __('messages.image') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th class="no-print">{{ __('messages.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userInitiatedSimpleUsers as $message)
                                <tr @if(!$message->is_read) style="background:#f9f9f9" @endif>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $message->counterparty->f_name ?? 'Unknown User' }}</strong>
                                        <br><small class="text-muted">{{ $message->counterparty->email }}</small>
                                    </td>
                                    <td>{{ Str::limit($message->message, 50) }}</td>
                                    <td>
                                        @if(!empty($message->image_path))
                                            <img src="{{ asset('storage/' . $message->image_path) }}" alt="image" style="max-height:40px;max-width:60px" />
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($message->is_read)
                                        <span class="badge badge-success">{{ __('messages.read') }}</span>
                                        @else
                                        <span class="badge badge-warning">{{ __('messages.unread') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $message->created_at->format('d M Y h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('admin.chat.show', $message->counterparty->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fa fa-eye"></i> {{ __('messages.open') }}
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    @if($userInitiatedSubscribers->count() == 0 && $userInitiatedSimpleUsers->count() == 0)
                    <div class="text-center text-muted">
                        <i class="fa fa-comment-slash fa-3x"></i>
                        <h4>{{ __('messages.no_user_initiated_chats') }}</h4>
                        <p>{{ __('messages.no_users_contacted_you') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Admin-Initiated Conversations -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-bd lobidrag">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-comment-plus"></i> 
                        {{ __('messages.conversations_started_by_admin') }}
                        <small class="text-muted">({{ __('messages.chats_you_started') }})</small>
                    </h4>
                </div>
                <div class="panel-body">
                    <!-- Subscribers admin contacted -->
                    @if($adminInitiatedSubscribers->count() > 0)
                    <h5><i class="fa fa-star text-warning"></i> {{ __('messages.subscribers') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.user') }}</th>
                                    <th>{{ __('messages.message') }}</th>
                                    <th>{{ __('messages.image') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th class="no-print">{{ __('messages.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($adminInitiatedSubscribers as $message)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $message->counterparty->f_name ?? 'Unknown User' }}</strong>
                                        <br><small class="text-muted">{{ $message->counterparty->email }}</small>
                                        <br><span class="badge badge-info">{{ __('messages.admin_initiated') }}</span>
                                    </td>
                                    <td>{{ Str::limit($message->message, 50) }}</td>
                                    <td>
                                        @if(!empty($message->image_path))
                                            <img src="{{ asset('storage/' . $message->image_path) }}" alt="image" style="max-height:40px;max-width:60px" />
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($message->is_read)
                                        <span class="badge badge-success">{{ __('messages.read') }}</span>
                                        @else
                                        <span class="badge badge-warning">{{ __('messages.unread') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $message->created_at->format('d M Y h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('admin.chat.show', $message->counterparty->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fa fa-eye"></i> {{ __('messages.open') }}
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    <!-- Simple Users admin contacted -->
                    @if($adminInitiatedSimpleUsers->count() > 0)
                    <h5><i class="fa fa-user text-info"></i> {{ __('messages.simple_users') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.user') }}</th>
                                    <th>{{ __('messages.message') }}</th>
                                    <th>{{ __('messages.image') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th class="no-print">{{ __('messages.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($adminInitiatedSimpleUsers as $message)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $message->counterparty->f_name ?? 'Unknown User' }}</strong>
                                        <br><small class="text-muted">{{ $message->counterparty->email }}</small>
                                        <br><span class="badge badge-info">{{ __('messages.admin_initiated') }}</span>
                                    </td>
                                    <td>{{ Str::limit($message->message, 50) }}</td>
                                    <td>
                                        @if(!empty($message->image_path))
                                            <img src="{{ asset('storage/' . $message->image_path) }}" alt="image" style="max-height:40px;max-width:60px" />
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($message->is_read)
                                        <span class="badge badge-success">{{ __('messages.read') }}</span>
                                        @else
                                        <span class="badge badge-warning">{{ __('messages.unread') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $message->created_at->format('d M Y h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('admin.chat.show', $message->counterparty->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fa fa-eye"></i> {{ __('messages.open') }}
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    @if($adminInitiatedSubscribers->count() == 0 && $adminInitiatedSimpleUsers->count() == 0)
                    <div class="text-center text-muted">
                        <i class="fa fa-comment-plus fa-3x"></i>
                        <h4>{{ __('messages.no_admin_initiated_chats') }}</h4>
                        <p>{{ __('messages.you_havent_started_any_chats') }}</p>
                        <a href="{{ route('admin.chat.start_new') }}" class="btn btn-success">
                            <i class="fa fa-plus"></i> {{ __('messages.start_new_chat') }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>