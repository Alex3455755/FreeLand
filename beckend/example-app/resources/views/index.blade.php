
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Объявления</title>
        <link rel="stylesheet" href="{{ asset('styles/main.css') }}">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h1 class="my-3 text-center">Объявления</h1>
        @if (count($sales) > 0)
        <table class = "table table-striped">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Цена, руб</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            <tbody>
                @foreach ($sales as $sale)
                                <tr>
                    <td><h3>{{ $sale-> title }}</h3></td>
                    <td>{{$sale->price}}</td>
                    <td>
                        <a href='{{ route("sale.detail", ["sale" => $sale-> id ]) }}'>Ссылка</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            </tbody>
        </table>
        @endif
    </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>

  </body>

</body>
</html>