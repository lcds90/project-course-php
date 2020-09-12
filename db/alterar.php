<div class="titulo">Alterar Registro</div>
<h2>Cadastro</h2>

<?php
ini_set('display_errors', 0);

require_once "conexao.php";
$conexao = novaConexao();

if($_GET['codigo']){
    $sql = "SELECT * FROM cadastro WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $_GET['codigo']);


    if($stmt->execute()){
        $resultado = $stmt->get_result();
        if($resultado->num_rows > 0){
            
            $dados = $resultado->fetch_assoc();
            if($dados['nascimento']){
                $dt = new DateTime($dados['nascimento']);
                $dados['nascimento'] = $dt->format('d/m/Y');
            }
            if($dados['salario']){
                $dados['salario'] = str_replace(".", ",", $dados['salario']);
            }
        }
    }
}

if (count($_POST) > 0) {
    $dados = $_POST;
    $erros = [];

    if (trim($dados['nome']) === "") {
        $erros['nome'] = 'Nome é obrigatório';
    }
    if (trim($dados['nascimento']) != "") {
        $data = DateTime::createFromFormat(
            'd/m/Y',
            $_POST['nascimento']
        );
        if (!$data) {
            $erros['nascimento'] = 'Data deve estar no padrão dd/mm/aaaa';
        }
    }

    if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $erros['email'] = 'Email inválido';
    }

    if (!filter_var($dados['site'], FILTER_VALIDATE_URL)) {
        $erros['site'] = 'Site inválido';
    }

    $filhosConfig = [
        "options" => ["min_range" => 0, "max_range" => 20]
    ];
    if (!filter_var($dados['filhos'], FILTER_VALIDATE_INT, $filhosConfig) && $dados['filhos'] != 0) {
        $erros['filhos'] = 'Quantidade de filhos inválida';
    }

    $salarioConfig = [
        "options" => ["decimal" => ","]
    ];
    if (!filter_var($dados['salario'], FILTER_VALIDATE_FLOAT, $salarioConfig)) {
        $erros['salario'] = 'Salário inválido';
    }

    if (!count($erros)) {
        $sql = "UPDATE cadastro SET nome = ?, nascimento = ?, email = ?, site = ?, filhos = ?, salario = ?
        WHERE id = ?";

        
        $stmt = $conexao->prepare($sql);
        $params = [
            $dados['nome'],
            $data ? $data->format('Y-m-d') : null,
            $dados['email'],
            $dados['site'],
            $dados['filhos'],
            $dados['salario'] ? str_replace(",", ".", $dados['salario']) : null,
            $dados['id'],
        ];
        $stmt->bind_param("ssssidi", ...$params);
        if ($stmt->execute()) {
            unset($dados);
        }
    }
}
?>

<form action="/cursos-php/view.php" method="get">
<input type="hidden" name="dir" value="db">
<input type="hidden" name="file" value="alterar">
<div class="form-group row">
    <div class="col-sm-10">
        <input type="number" name="codigo" class="form-control"
        value="<?= $_GET['codigo']?>"
        placeholder="Informe o código para consultar">
    </div>
<div class="col-sm-2">
    <button class="btn btn-success mb-4">Consultar</button>
</div>
</div>
</form>
<form action="#" method="post">
    <input type="hidden" name="id" value="<?= $dados['id']?>">
    <div class="form-row">
        <div class="form-group col-md-9">
            <label for="nome">Nome</label>
            <input type="text" name="nome" id="nome" class="form-control <?= $erros['nome'] ? 'is-invalid' : '' ?>" value="<?= $dados['nome'] ?>" placeholder="Nome">
            <div class="invalid-feedback">
                <?= $erros['nome'] ?>
            </div>
        </div>
        <div class="form-group col-md-3">
            <label for="nascimento">Nascimento</label>
            <input type="text" name="nascimento" id="nascimento" class="form-control <?= $erros['nascimento'] ? 'is-invalid' : '' ?> " value="<?= $dados['nascimento'] ?>" placeholder="Nascimento">
            <div class="invalid-feedback">
                <?= $erros['nascimento'] ?>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="email">E-mail</label>
            <input type="text" name="email" id="email" class="form-control <?= $erros['email'] ? 'is-invalid' : '' ?>" value="<?= $dados['email'] ?>" placeholder="Email">
            <div class="invalid-feedback">
                <?= $erros['email'] ?>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="nascimento">Site</label>
            <input type="text" name="site" id="site" class="form-control <?= $erros['site'] ? 'is-invalid' : '' ?>" value="<?= $dados['site'] ?>" placeholder="Site">
            <div class="invalid-feedback">
                <?= $erros['nascimento'] ?>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="filhos">Quantidade Filhos</label>
            <input type="text" name="filhos" id="filhos" class="form-control <?= $erros['filhos'] ? 'is-invalid' : '' ?>" value="<?= $dados['filhos'] ?>" placeholder="Quantidade de Filhos">
            <div class="invalid-feedback">
                <?= $erros['filhos'] ?>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="salario">Salário</label>
            <input type="text" name="salario" id="salario" class="form-control <?= $erros['salario'] ? 'is-invalid' : '' ?>" value="<?= $dados['salario'] ?>" placeholder="Salário">
            <div class="invalid-feedback">
                <?= $erros['salario'] ?>
            </div>
        </div>
    </div>
    <button class="btn btn-success btn-lg">Enviar</button>
</form>