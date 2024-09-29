<!DOCTYPE html>
<html>
<head>
    <title>Generation PDF E-Sim - gtesim.moov-africa</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<style type="text/css">
    html { margin: 0px; }
    @page { margin:0px; }
    .body{
        background:#eee;
        margin: 0px;
    }
    .text-danger strong {
        color: #9f181c;
    }
    .receipt-main {
        background: #ffffff none repeat scroll 0 0;
        border-bottom: 2px solid #333333;
        border-top: 2px solid #fcf9f9;
        margin-top: 5px;
        margin-bottom: 5px;
        margin-left: 5px;
        padding: 5px 5px !important;
        position: center;
        box-shadow: 0 1px 21px #acacac;
        color: #333333;
        font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
    }
    .receipt-main p {
        color: #333333;
        font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
        line-height: 1.42857;
    }
    .receipt-footer h1 {
        font-size: 15px;
        font-weight: 400 !important;
        margin: 0 !important;
    }
    .receipt-main::after {
        background: #414143 none repeat scroll 0 0;
        content: "";
        height: 5px;
        left: 0;
        position: center;
        right: 0;
        top: -13px;
    }
    .receipt-main thead {
        background: #414143 none repeat scroll 0 0;
    }
    .receipt-main thead th {
        color:#fff;
    }
    .receipt-right h5 {
        font-size: 12px;
        font-weight: bold;
        margin: 0 0 7px 0;
    }
    .receipt-right p {
        font-size: 10px;
        margin: 0px;
    }
    .receipt-right p i {
        text-align: center;
        width: 16px;
    }
    .receipt-main td {
        padding: 4px 15px !important;
    }
    .receipt-main th {
        padding: 8px 15px !important;
    }
    .receipt-main td {
        font-size: 10px;
        font-weight: initial !important;
    }
    .receipt-main td p:last-child {
        margin: 0;
        padding: 0;
    }
    .receipt-main td h2 {
        font-size: 16px;
        font-weight: 900;
        margin: 0;
        text-transform: uppercase;
    }
    .receipt-header-mid .receipt-left h1 {
        font-weight: 100;
        margin: 34px 0 0;
        text-align: right;
        text-transform: uppercase;
    }
    .receipt-header-mid {
        margin: 24px 0;
        overflow: hidden;
    }

    .noborder td, .noborder th {
        border: none !important;
    }

    #container {
        background-color: #dcdcdc;
    }

</style>
<body>
<div class="container py-5">
    <div class="col-md-12">
        <div class="row">
            <div class="receipt-main col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">

                <div>

                    <div class="row">
                        <table class="table noborder" style="border: 0;">
                            <tbody>
                            <tr>
                                <td class="col-md-8"><h5>Moov-Africa E-sim</h5></td>
                                <td class="col-md-4 text-right"><img alt="logo" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}" style="width: 91px; border-radius: 4px;"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th colspan="2" class="text text-center">Infos Client</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr>
                            <td class="col-md-4">Nom / Raison Sociale</td>
                            <td class="col-md-8">{{ $phonenum->hasphonenum->nom_raison_sociale }}</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Prenom</td>
                            <td class="col-md-8">{{ $phonenum->hasphonenum->prenom }}</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Email</td>
                            <td class="col-md-8">{{ $phonenum->hasphonenum->latestEmailAddress->email_address }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th colspan="2" class="text text-center">Indentification Profile E-sim</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="col-md-4">Numero Appel</td>
                            <td class="col-md-8">{{ $phonenum->phone_number }}</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">ICCID</td>
                            <td class="col-md-8">{{ $phonenum->esim->iccid }}</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Numero PIN</td>
                            <td class="col-md-8">{{ $phonenum->esim->pin }}</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Numero PUK</td>
                            <td class="col-md-8">{{ $phonenum->esim->puk }}</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Code Activation</td>
                            <td class="col-md-8">{{ $phonenum->esim->ac }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col text-center">
                        <p> <small> Scannez ce QR code pour telecharger votre profile E-SIM </small> </p>
                        <p>
                            <img src="data:image/png;base64, {{ base64_encode(QrCode::format('png')->size(150)->generate($phonenum->esim->ac)) }} ">
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col text-center">
                        <img alt="logo" src="data:imae/png;base64,{{ base64_encode(file_get_contents(public_path('images/mode_operatoire.PNG'))) }}" style="width: 530px;">
                    </div>
                </div>

                @if(!isset($generate_now))
                    <div class="row">
                        <div class="col-md-4 text-left">
                            <a href="/clientesims/{{ $phonenum->hasphonenum->uuid }}/show" class="btn btn-sm btn-secondary text-left">Retour</a>
                        </div>
                        <div class="col-md-4 text-center">
                            <a href="{{ route('clientesims.preprintpdf', $phonenum->id) }}" class="btn btn-sm btn-success text-left">Imprimer PDF</a>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="{{ route('clientesims.generatepdf', $phonenum->id) }}" class="btn btn-sm btn-primary text-right">Télécharger PDF</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</body>

</html>
