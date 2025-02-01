<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Boostrap Starter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container" style="margin-top: 10px;">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center">Welcome to Shipping Label Extractor</h1>
            </div>

            {{-- upload file --}}
            <div class="col-md-12">
                @if (Session::has('success'))
                    <div class="alert alert-success" role="alert">
                        {{ Session::get('success') }}
                    </div>
                @endif
            </div>

            <div class="col-md-12">
                @if (Session::has('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ Session::get('error') }}
                    </div>
                @endif
            </div>

            <div class="col-md-12">
                <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row justify-content-center align-items-end">
                        <div class="col-md-3">
                            {{-- select --}}
                            <div class="form-group">
                                <label for="service">Service</label>
                                <select name="service" class="form-control @error('service') is-invalid @enderror">
                                    <option value="">Choose Service</option>
                                    <option value="spx">SPX</option>
                                </select>

                                @error('service')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="file">Choose File</label>
                                <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">

                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- show data --}}
            <div class="col-md-12 mt-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Awb Number</th>
                            <th>No Reference</th>
                            <th>Service</th>
                            <th>Weight</th>
                            <th>Delivery Date</th>
                            <th class="text-center" colspan="2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                            <tr>
                                <td>{{ $data->awb_number }}</td>
                                <td>{{ $data->no_reference }}</td>
                                <td>{{ $data->shipping_service }}</td>
                                <td>{{ $data->weight }}</td>
                                <td>{{ $data->delivery_date }}</td>
                                <td>{{ $data->status }}</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-sm">Print</a>
                                    <a href="#" class="btn btn-info btn-sm">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
