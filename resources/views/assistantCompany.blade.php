@extends('layouts.general')

@section('title', __('assistantInst.inst_title'))


@section('content')
    <h2 class="section-title"><span>{{ __('assistantInst.inst_title') }}</span></h2>

    <h3>{{ __('assistantInst.inst_issuer-name') }}{{ $company->name }}</h3>
        <p>{{ __('assistantInst.inst_issuer-url') }}: <a href="{{ $company->url }}" target="_blank">{{ $company->url }}</a><br />
	{{ __('assistantInst.inst_issuer-email') }}: {{ $company->email }}<br />
	{{ __('assistantInst.inst_issuer-id') }}: <span style="font-size: 0.75em;">[{{ $company->issuerID }}]</span>
        </p>


    {{-- custom fields --}}

    @if ($customFields && $customFields['header']['name'] == 'issuerCustomFileds')
    <div class="bd-callout bd-callout-info">
        <p>{{ __('assistantInst.inst_issuer-custom-fields') }}</p>
    </div>
    <p>
        @foreach ($customFields['form'] as $field)
            @if ($field['type'] == 'text')
		{{ $field['name'] }}: {{ $field['content'] }}<br />
            @endif
        @endforeach
    </p>
    @endif

    <div class="container">
        <div class="alert alert-success mt-5" role="alert">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-md-4 col-sm-4 col-xs-6 flex-fill">
                    <a class="btn btn-primary" href="{{ env('APP_URL') }}/assistantCreatePreAssertion" role="button">{{ __('assistantInst.inst_create-pre-assrt-btn') }}</a>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-6 flex-fill">
                    <a class="btn btn-primary" href="{{ env('APP_URL') }}/assistantShowPreAssertions" role="button">{{ __('assistantInst.inst_show-pre-assrt-btn') }}</a>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-6 flex-fill">
                    <a class="btn btn-primary" href="{{ env('APP_URL') }}/assistantShowAssertions" role="button">{{ __('assistantInst.inst_show-assrt-btn') }}</a>
                </div>
            </div>
	</div>
    </div>

    <h3>{{ __('assistantInst.inst_badge-classes-title') }}</h3>
    <table class="table">
        <thead>
            <tr>
		<th scope="col">#</th>
		{{-- <th scope="col">Badge Class ID</th> --}}
                <th scope="col">{{ __('assistantInst.inst_bc-sum-th-name') }}</th>
                <th scope="col">{{ __('assistantInst.inst_bc-sum-th-date') }}</th>
                <th scope="col">{{ __('assistantInst.inst_bc-sum-th-func') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($badgeclasses as $badgeclass)
                <tr>
                    <th scope="row">{{ $loop->index + 1 }}</th>
                    {{-- <td>{{ $badgeclass->badgeclassID }}</td> --}}
                    <td style="width:43%">
                        <strong>{{ $badgeclass->name }}</strong><br />
			<?php
			    if(strlen($badgeclass->description) < 80)
				$bcd = $badgeclass->description;
			    else
				$bcd = substr($badgeclass->description,0,80)." ...";
			?>
			{{ $bcd }}
                    </td>

                    <td>{{ $badgeclass->created_at }}</td>
                    <td>
			<a class="btn btn-primary" href="/issuerbadges/{{ $badgeclass->badgeclassID }}/info"
                            role="button">{{ __('assistantInst.inst_bc_btn-info') }}&nbsp;&nbsp;<i class="bi-info-circle"></i></a>

                        <?php
                        
                        $tmpAssertion = App\Models\Assertion::where('badgeclassID', '=', $badgeclass->badgeclassID)->count();
                        $tmpPreAssertion = App\Models\Preassertion::where('badgeclassID', '=', $badgeclass->badgeclassID)->count();
                        
                            //ddd($tmpPreAssertion);

                        ?>

                        @if (!($tmpAssertion || $tmpPreAssertion))
			    <a class="btn btn-primary" href="/issuerbadges/{{ $badgeclass->badgeclassID }}/settype"
                                role="button">{{ __('assistantInst.inst_bc_btn-set-type') }}&nbsp;&nbsp;<i class="bi-file-earmark-check-fill"></i></a>
                        @endif



                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>


    <h2 class="section-title"><span>
            <p class="text-center"><a href="{{ env('APP_URL') }}/assistantMain" class="btn btn-action">{{ __('assistantInst.inst_back') }}</a></p>
        </span></h2>

@endsection
