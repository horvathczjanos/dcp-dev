@extends('layouts.general')

@section('title', __('issuerBadgeStack.bst_page-title'))


@section('content')

    <h2 class="section-title"><span>{{ __('issuerBadgeStack.bst_page-title') }} </span></h2>

    <div class="row">
        <div class="col-12 text-center">

            <img src="data:image/png;base64,{{ base64_encode($badgeImage) }}" style="width: 150px;" alt="badge image">
            <p><strong>{{ $badgeName }}</strong><br />

            </p>
        </div>
    </div>


    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            @if (count($inStack))
                <p class="text-center"><strong>{{ __('issuerBadgeStack.bst_in-stack') }}</strong><br />
                {{ App\Models\Stackmember::where('stackmemberID', '=', $inStack[0]['stackmemberID'])->first()->name }}<br />
                <i>{{ App\Models\Stackmember::where('stackmemberID', '=', $inStack[0]['stackmemberID'])->first()->description }}</i></p>
            @else
                <p class="text-center"><strong>{{ __('issuerBadgeStack.bst_no-stack') }}</strong></p>
            @endif
        </div>
    </div>

        <div class="bd-callout bd-callout-info">
            <p>{{ __('issuerBadgeStack.bst_select-stack-member') }}</p>
        </div>

    <form method="POST" action="doStack" enctype="multipart/form-data">
        @csrf

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <strong>{{ $message }}</strong>

            </div>
        @endif

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ __('issuerBadgeStack.bst_th-sel') }}</th>
                    <th scope="col">{{ __('issuerBadgeStack.bst_th-sm-name') }}</th>
                    <th scope="col">{{ __('issuerBadgeStack.bst_th-sm-descr') }}</th>
                    <th scope="col">{{ __('issuerBadgeStack.bst_th-sm-role') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>0</td>
                    <td scope="col">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="stackmember" id="0" value="0"
                            @if (!count($inStack)) checked @endif>
                        </div>
                    </td>
                        <td scope="col">{{ __('issuerBadgeStack.bst_td-nop-name') }}</td>
	                <td scope="col">{{ __('issuerBadgeStack.bst_td-nop-descr') }}</td>
                        <td scope="col">{{ __('issuerBadgeStack.bst_td-nop-role') }}</td>
                </tr>
                @foreach ($stackMembers as $item)
                    <tr>
                        <td scope="col">{{ $loop->index + 1 }}</td>
                        <td scope="col">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="stackmember"
                                    id="{{ $item->stackmemberID }}" value="{{ $item->stackmemberID }}"
                                    @if (count($inStack) && $item->stackmemberID == $inStack[0]['stackmemberID']) checked @endif>
                            </div>
                        </td>
                        <td scope="col">{{ $item->name }}</td>
                        <td scope="col">{{ $item->description }}</td>
                        <td scope="col">
                            <?php
                            $rolesToThisStackMembers = App\Models\Stackrole::where('part1', '=', $item->stackmemberID)->get();
                            
                            ?>
                            @foreach ($rolesToThisStackMembers as $role)
                                {{ $role->name }} <br />
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>



        <div class="bd-callout bd-callout-info">
            <p>{{ __('issuerBadgeStack.bst_btn-lbl') }}</p>
	</div>
            <p class="text-center ">
		<button type="submit" class="btn btn-primary" value="set">
		    {{ __('issuerBadgeStack.bst_btn') }}&nbsp;&nbsp;<i class="bi-save"></i>
		</button>
            </p>
        </div>
    </form>

    <h2 class="section-title"><span>
            <p class="text-center"><a href="./../../company/{{ session()->get('issuerID') }}" class="btn btn-action">{{ __('issuerBadgeStack.bst_btn-back') }}</a></p> 
        </span></h2>

@endsection
