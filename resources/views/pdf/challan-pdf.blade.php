<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Port Logistics') }}</title>
    </head>
    <style>
        td {
            padding:10px;
        }
    </style> 
</head>
<body style="margin: 0; font-family: 'Source Sans Pro', sans-serif; font-size: 14px;">
    <table style="border-collapse: collapse; width:100%; border: none; text-align: center;">
        <tr>
            <td style=" padding: 20px 0;">
                <div style="max-width: 600px; margin: 0 auto;border: 1px solid #41719c; padding:0px;">
                    <table style="border-collapse: collapse; width: 100%; padding: 0px; border: none;">
                        <tbody>
                            <tr>
                                <td>
                                    <table style="border-collapse: collapse; padding: 0px; border: none; width:100%;">
                                        <tr>
                                            <th style="background:#203464; text-align: center;color: #fff;border-collapse: collapse;">
                                                <h3>Challan</h3>
                                            </th>
                                        </tr>
                                    </table>
                                    <table class="column-1-2" style="border-collapse: collapse;border-bottom: 1px solid #213965; padding: 0px; width:100%; text-align: left;">
                                        <tbody>
                                            <tr>
                                                <td style="width:25%;vertical-align:top;">
                                                    <h5 style="margin: 0">Challan No:</h5>
                                                    <p style="margin: 0">{{$request['challan_no']}}</p>
                                                </td>
                                                <td style="text-align: center; width:50%;vertical-align:top;">
                                                    <h1 style="margin: 0">{{$request['org_name']}}</h1>
                                                    <p>{{$request['org_address']}}</p>
                                                </td>
                                                <td style="width:25%;vertical-align:top;">
                                                    <h5 style="margin: 0">{{$request['loaded_at']}} <br>Shift:{{$request['shift_name']}}</h5>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="column-1-2" style="border-collapse: collapse;width:100%; text-align: left;border: none;padding:0px;">
                                        <tbody>
                                            <tr>
                                                <td style="width:50%;vertical-align:top;">
                                                    <h5><strong>Place form: </strong>{{$request['origin']}}</h5>
                                                </td>
                                                <td style="width:50%;text-align:right;vertical-align:top;">
                                                    <h5><strong>Place to: </strong>{{$request['destination']}}</h5>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="column-1-2" style="border-collapse: collapse;width:100%; text-align: left;border: none;padding:0px;">
                                        <tbody>
                                            <tr>
                                                <td style="width:50%;vertical-align:top;">
                                                    <h5><strong>Vessel: </strong>{{$request['vessel_name']}}</h5>
                                                </td>
                                                <td style="width:50%;text-align:right;vertical-align:top;">
                                                    <img src="{{$request['barcode_path']}}" alt="barcode" style="width:150px;height:30px;"/>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="column-1-2" style="border-collapse: collapse;width:100%; text-align: left;border: none;padding:0px;">
                                        <tbody>
                                            <tr>
                                                <td style="width:50%;vertical-align:top;">
                                                    <h5><strong>Cargo: </strong>{{$request['cargo_name']}}</h5>
                                                </td>
                                                <td style="width:50%;vertical-align:top;"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="column-1-2" style="border-collapse: collapse;width:100%; text-align: left;border: none;padding:0px;">
                                        <tbody>
                                            <tr>
                                                <td style="width:50%;vertical-align:top;">
                                                    <h5 style="margin: 0"><strong>Truck/Dumper No: </strong> {{$request['truck_no']}}</h5>
                                                </td>
                                                <td style="width:50%;border-top: 1px solid #213965;vertical-align:top;">
                                                    <p style="text-align:center;width:100%;"><strong>Signature</strong></p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
