@extends('layouts.general')

@section('title', __('issuerInst.inst_title'))

@section('content')

    <h2 class="section-title">
        <span>
            {{ __('issuerInst.inst_title') }}
        </span>
    </h2>


    {{-- Issuer main data --}}

    <h3>
        {{ __('issuerInst.inst_issuer-name') }}
        {{ $company->name }}
    </h3>

    <p>

        {{ __('issuerInst.inst_issuer-url') }}:

        <a href="{{ $company->url }}" target="_blank" rel="noopener noreferrer">
            {{ $company->url }}
        </a>

        <br />

        {{ __('issuerInst.inst_issuer-email') }}:
        {{ $company->email }}

        <br />

        {{ __('issuerInst.inst_issuer-id') }}:

        <span style="font-size: 0.75em;">
            [{{ $company->issuerID }}]
        </span>

    </p>


    {{-- Custom fields --}}

    @if ($customFields && isset($customFields['header']['name']) && $customFields['header']['name'] == 'issuerCustomFileds')

        <div class="bd-callout bd-callout-info">

            <p>
                {{ __('issuerInst.inst_issuer-custom-fields') }}
            </p>

        </div>


        <p>

            @foreach ($customFields['form'] as $field)
                @if ($field['type'] == 'text')
                    {{ $field['name'] }}:
                    {{ $field['content'] }}

                    <br />
                @endif
            @endforeach

        </p>

    @endif



    {{-- Main functions --}}

    <div class="container">

        <div class="alert alert-success mt-5" role="alert">

            <div class="row d-flex justify-content-center align-items-center">

                <div class="col-md-2 col-sm-3 col-xs-6 flex-fill">

                    <a class="btn btn-primary" href="{{ url('/createBadgeclass') }}" role="button">
                        {{ __('issuerInst.inst_new-class-btn') }}
                    </a>

                </div>


                <div class="col-md-2 col-sm-3 col-xs-6 flex-fill">

                    <a class="btn btn-primary" href="{{ url('/bulkPreAssertionUpload') }}" role="button">
                        {{ __('issuerInst.inst_bulk-upload-btn') }}
                    </a>

                </div>


                @if ($company->issuerID == '3062aef14c677cc98f068cef3afe3add686d419837e42b81ba0d44195072a776')
                    <div class="col-md-2 col-sm-3 col-xs-6 flex-fill">

                        <a class="btn btn-primary" href="{{ url('/greencertAssertionUpload') }}" role="button">
                            {{ __('issuerInst.inst_greencert-upload-btn') }}
                        </a>

                    </div>
                @endif


                <div class="col-md-2 col-sm-3 col-xs-6 flex-fill">

                    <a class="btn btn-primary" href="{{ url('/createPreAssertion') }}" role="button">
                        {{ __('issuerInst.inst_create-pre-assrt-btn') }}
                    </a>

                </div>


                <div class="col-md-2 col-sm-3 col-xs-6 flex-fill">

                    <a class="btn btn-primary" href="{{ url('/showPreAssertions') }}" role="button">
                        {{ __('issuerInst.inst_show-pre-assrt-btn') }}
                    </a>

                </div>


                <div class="col-md-2 col-sm-3 col-xs-6 flex-fill">

                    <a class="btn btn-primary" href="{{ url('/showAssertions') }}" role="button">
                        {{ __('issuerInst.inst_show-assrt-btn') }}
                    </a>

                </div>

            </div>

        </div>

    </div>


    {{-- Metabadge functions --}}

    <div class="alert alert-success mt-5" role="alert">

        <div class="row d-flex justify-content-center align-items-center">

            <div class="col-md-2 col-sm-3 col-xs-6 flex-fill">

                <a class="btn btn-primary" href="{{ url('/runIssuerMetabadgeRules') }}" role="button">
                    Run Metabadge Assertion Rules
                </a>

            </div>


            <div class="col-md-2 col-sm-3 col-xs-6 flex-fill">

                <a class="btn btn-primary" href="{{ url('/setIssuerMetabadgeRules') }}" role="button">
                    Set Metabadge Assertion Rules
                </a>

            </div>


            <div class="col-md-2 col-sm-3 col-xs-6 flex-fill">

                <a class="btn btn-primary" href="{{ url('/newIssuerMetabadgeRules') }}" role="button">
                    Add new Metabadge Assertion Rules
                </a>

            </div>

        </div>

    </div>

    <style>
        .issuer-summary-card {
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .issuer-summary-card:hover {
            transform: none;
        }

        .issuer-summary-scroll {
            height: clamp(340px, 46vh, 520px);
            overflow-y: auto;
            overflow-x: auto;
        }

        .issuer-summary-scroll thead th {
            position: sticky;
            top: 0;
            background-color: white;
            z-index: 2;
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.08);
        }

        .issuer-summary-card .table {
            margin-bottom: 0;
        }

        .issuer-summary-description {
            color: #6c757d;
            font-size: 0.9em;
        }

        .issuer-summary-count {
            font-size: 0.9em;
        }
    </style>



    {{-- Badgeclass box --}}

    <div class="card issuer-summary-card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <strong>
                {{ __('issuerInst.inst_badge-classes-title') }}
            </strong>


            <span class="badge bg-secondary issuer-summary-count">
                {{ $numberOfBadgeclasses }}
            </span>

        </div>


        <div class="card-body p-0 issuer-summary-scroll">

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th scope="col">
                            #
                        </th>

                        <th scope="col">
                            {{ __('issuerInst.inst_bc-sum-th-name') }}
                        </th>

                        <th scope="col">
                            {{ __('issuerInst.inst_bc-sum-th-date') }}
                        </th>

                        <th scope="col">
                            {{ __('issuerInst.inst_bc-sum-th-func') }}
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach ($badgeclasses as $badgeclass)
                        <tr>

                            <th scope="row">

                                {{ $loop->index + 1 }}

                            </th>


                            <td style="width: 43%;">

                                <strong>
                                    {{ $badgeclass->name }}
                                </strong>

                                <br />


                                <span class="issuer-summary-description">

                                    @if (strlen($badgeclass->description) < 80)
                                        {{ $badgeclass->description }}
                                    @else
                                        {{ substr($badgeclass->description, 0, 80) }}
                                        ...
                                    @endif

                                </span>

                            </td>


                            <td>

                                {{ $badgeclass->created_at }}

                            </td>


                            <td>

                                <a class="btn btn-primary" href="/issuerbadges/{{ $badgeclass->badgeclassID }}/info"
                                    role="button">

                                    {{ __('issuerInst.inst_bc_btn-info') }}

                                    &nbsp;&nbsp;

                                    <i class="bi-info-circle"></i>

                                </a>


                                <a class="btn btn-primary" href="/issuerbadges/{{ $badgeclass->badgeclassID }}/stack"
                                    role="button">

                                    {{ __('issuerInst.inst_bc_btn-stack') }}

                                    &nbsp;&nbsp;

                                    <i class="bi-diagram-2-fill"></i>

                                </a>


                                @php

                                    $tmpAssertion = $badgeclassAssertionCounts[$badgeclass->badgeclassID] ?? 0;

                                    $tmpPreAssertion = $badgeclassPreassertionCounts[$badgeclass->badgeclassID] ?? 0;

                                @endphp


                                @if (!($tmpAssertion || $tmpPreAssertion))
                                    <br />
                                    <br />


                                    <a class="btn btn-primary" href="/issuerbadges/{{ $badgeclass->badgeclassID }}/settype"
                                        role="button">

                                        {{ __('issuerInst.inst_bc_btn-set-type') }}

                                        &nbsp;&nbsp;

                                        <i class="bi-file-earmark-check-fill"></i>

                                    </a>


                                    <a class="btn btn-primary" href="/issuerbadges/{{ $badgeclass->badgeclassID }}/delete"
                                        role="button">

                                        {{ __('issuerInst.inst_bc_btn-delete') }}

                                        &nbsp;&nbsp;

                                        <i class="bi-eraser-fill"></i>

                                    </a>


                                    <br />
                                    <br />


                                    <a class="btn btn-primary" href="/issuerbadges/{{ $badgeclass->badgeclassID }}/edit"
                                        role="button">

                                        {{ __('issuerInst.inst_bc_btn-edit') }}

                                        &nbsp;&nbsp;

                                        <i class="bi-pencil-fill"></i>

                                    </a>


                                    <a class="btn btn-primary"
                                        href="/issuerbadges/{{ $badgeclass->badgeclassID }}/multilangSettings"
                                        role="button">

                                        {{ __('issuerInst.inst_bc_btn-multilangsettings') }}

                                        &nbsp;&nbsp;

                                        <i class="bi-translate"></i>

                                    </a>
                                @endif

                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>

        </div>


        <div class="card-footer d-flex justify-content-between align-items-center">

            <span> </span>

            <a class="btn btn-primary btn-sm" href="{{ url('/createBadgeclass') }}">

                {{ __('issuerInst.inst_new-class-btn') }}

            </a>

        </div>

    </div>



    {{-- Assertion section --}}

    <div class="card issuer-summary-card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <strong>
                {{ __('issuerInst.inst_assrt-title') }}
            </strong>


            <span class="badge bg-secondary issuer-summary-count">
                {{ $numberOfAssertions }}
            </span>

        </div>


        <div class="card-body p-0 issuer-summary-scroll">

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th scope="col">
                            #
                        </th>

                        <th scope="col">
                            {{ __('issuerInst.inst_assrt-sum-th-name') }}
                        </th>

                        <th scope="col">
                            {{ __('issuerInst.inst_assrt-sum-th-rcp') }}
                        </th>

                        <th scope="col">
                            {{ __('issuerInst.inst_assrt-sum-th-date') }}
                        </th>

                        <th scope="col">
                            {{ __('issuerInst.inst_assrt_sum-th-ids') }}
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach ($assertions as $assertion)
                        <tr>

                            <th scope="row">

                                {{ $loop->index + 1 }}

                            </th>


                            <td>

                                {{ $assertionBadgeclassNames[$assertion->badgeclassID] ?? 'NA' }}

                            </td>


                            <td>

                                @if ($assertion->originalIdentity)
                                    {{ $assertion->originalIdentity }}
                                @else
                                    NA
                                @endif

                            </td>


                            <td>

                                {{ $assertion->issuedOn }}

                            </td>


                            <td>

                                <span style="font-size: 0.75em;">

                                    {{ __('issuerInst.inst_assrt_bc-id-lbl') }}:

                                    {{ $assertion->badgeclassID }}

                                    <br />


                                    {{ __('issuerInst.inst_assrt_assrt-id-lbl') }}:

                                    {{ $assertion->assertionID }}

                                </span>

                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>

        </div>


        <div class="card-footer d-flex justify-content-between align-items-center">

            <span> </span>

            <a class="btn btn-primary btn-sm" href="{{ url('/showAssertions') }}" role="button">

                {{ __('issuerInst.inst_show-assrt-btn') }}

            </a>

        </div>

    </div>


    {{-- Back section --}}

    <h2 class="section-title">

        <span>

            <p class="text-center">

                <a href="{{ url('/issuerMain') }}" class="btn btn-action">

                    {{ __('issuerInst.inst_back') }}

                </a>

            </p>

        </span>

    </h2>

@endsection
