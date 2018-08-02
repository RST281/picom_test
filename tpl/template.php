<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link <?= ($this->action == 'load_from_file' ? 'active' : '')?>" href="/?action=load_from_file">Файл</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($this->action == 'load_from_db' ? 'active' : '')?>" href="/?action=load_from_db">База данных</a>
        </li>
    </ul>
</nav>
<table class="table">
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">Имя</th>
        <th scope="col">Фамилия</th>
        <th scope="col">Телефон</th>
        <th scope="col">Email</th>
        <th scope="col">Адрес</th>
        <th scope="col">Зарегистрирован</th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>
    <?php $this->showUsersTable(); ?>
    </tbody>
</table>
<div class="container-fluid">
    <?php $this->showGenerateButton(); ?>
</div>
</body>
</html>