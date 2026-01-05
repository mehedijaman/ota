@extends('report-layout')

@section('content')
    {{-- <x-report-header :header="$report['header']"></x-report-header> --}}

    <div class="flex flex-col items-center">
        <div class="text-center font-bold">
            <i class="fa fa-user"></i>
            Employee List
        </div>

        <table>
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Joining Date</th>
                    <th>Phone</th>
                </tr>
            </thead>

            <tbody>
                <?php $serial = 0; ?>

                @foreach ($report['data'] as $employee)
                    <?php $serial += 1; ?>

                    <tr>
                        <td class="px-1">{{ $serial }}</td>
                        <td class="px-1">{{ $employee['code'] }}</td>
                        <td class="px-1">{{ $employee['name'] }}</td>
                        <td class="px-1">
                            {{ $employee['designation']?->name }}
                        </td>
                        <td class="px-1">
                            {{ date('d/m/Y', strtotime($employee['joining_date'])) }}
                        </td>
                        <td class="px-1">{{ $employee['phone'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
