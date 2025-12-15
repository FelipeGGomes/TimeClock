<!DOCTYPE html>
<html>
<body>
    <h1>Olá, {{ $user->name }}</h1>
    <p>Identificamos uma inconsistência no seu ponto do dia {{ $date }}.</p>
    
    <p>Os seguintes registros parecem estar faltando:</p>
    <ul>
        @foreach($missingTypes as $type)
            <li>{{ ucfirst(str_replace('_', ' ', $type)) }}</li>
        @endforeach
    </ul>

    <p>Por favor, acesse o sistema e faça a justificativa.</p>
</body>
</html>