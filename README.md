# CodeIgniter4 Modular Autoloader

Torne qualquer projeto **CodeIgniter 4** verdadeiramente modular – sem precisar editar `app/Config/Autoload.php` a cada novo módulo.

---

## 🐞 Problema – por que o autoloader “nativo” não basta

O CodeIgniter 4 segue estritamente a especificação **PSR‑4**, na qual **cada** raiz de _namespace_ precisa apontar para **um** diretório físico.  
Isso traz duas limitações práticas quando tentamos organizar a aplicação em múltiplos módulos:

1. **Cadastro manual de cada módulo**  
   Para que o autoloader reconheça um novo módulo é preciso abrir `app/Config/Autoload.php` e acrescentar mais uma linha à matriz `$psr4`, por exemplo:

   ```php
   'App\Blog'  => ROOTPATH . 'Modules/Blog',
   'App\Admin' => ROOTPATH . 'Modules/Admin',
   // … +1 linha por módulo
   ```

   Em projetos com dezenas de módulos isso vira um festival de entradas repetitivas – e cada _merge_ ou _deploy_ fica sujeito a conflitos ou esquecimentos.

2. **Subpastas NÃO são reconhecidas automaticamente**  
   Uma tentativa ingênua de “mapear tudo de uma vez” como:

   ```php
   'App\Modules' => ROOTPATH . 'Modules',
   ```

   não funciona: o autoloader passa a considerar **Modules** o namespace raiz e **para de descer** para `Modules/Blog`, `Modules/Admin`, etc.  
   O resultado é que classes, helpers e até `Config/Routes.php` **não são encontrados**, gerando erros _404_ ou **Class not found**.

> **Resumo:** cada novo módulo exige intervenção manual em arquivo de configuração e qualquer esquecimento quebra a aplicação. Escalar (ou simplesmente clonar) um projeto modular torna‑se trabalhoso e propenso a erros.

---

## 🚀 Solução

Este pacote Composer, faz alterações simples que altera o autoloader de modo a fazer uma varredura recursiva em qualquer pasta de módulos que você definir (por padrão: `APPPATH/Modules`).
Cada subpasta de primeiro nível vira um **módulo autônomo** com sua estrutura própria `Controllers/`, `Models/`, `Views/`, `Config/Routes.php` etc. – sem necessidade de alterar manualmente o `Config/Autoload.php`.

```
my‑project/
├── app/
│   └── …
└── Modules/            ← pasta monitorada
    ├── Blog/           ← módulo 1
    │   ├── Controllers/
    │   ├── Models/
    │   ├── Views/
    │   └── Config/Routes.php
    └── Admin/          ← módulo 2
        └── …
```

---

## 📦 Instalação

```bash
composer require rahpt/ci4modules
```

> **Requisito:** CodeIgniter 4.3 ou superior.

---

## ⚙️ Configuração

### 1. Habilitar o _Service Provider_

Após a instalação, execute o comando abaixo para atualizar o arquivo app/Config/Modules.php e escolher a pasta onde seus módulos residirão:

### 2. (Opcional) Alterar o caminho dos módulos

Padrão:

```php
APPPATH . 'Modules'   // ou seja, app/Modules
```

Para usar outro local (ex.: `ROOTPATH . 'modules'`), crie ou altere o arquivo `app/Config/ModulesTemplate.php`:

```php
<?php

namespace Config;

use Vendor\ModularAutoloader\Config\BaseModules; // ajuste o namespace real

class Modules extends BaseModules
{
    /**
     * Caminho absoluto onde os módulos residem.
     */
    public string $modulesPath = ROOTPATH . 'modules';  // altere nesta linha
}
```

---

## ✨ Como funciona

1. **Boot** – um _service provider_ é registrado pelo Composer.  
2. **Scan** – durante o _startup_, ele percorre `modulesPath` (nível 1).
3. **Register** – para cada subpasta encontrada, gera dinamicamente uma entrada PSR‑4 no autoloader interno do CI4:

   ```
   <AppNamespace>\<ModuleName>\  => <modulesPath>/<ModuleName>/
   ```

4. **Profit!** Controllers, Models, Configs, Helpers etc. do módulo ficam prontos para uso.

---

## 🖥️ Exemplo de rotas em um módulo

`Modules/Blog/Config/Routes.php`

```php
<?php

/** @var \CodeIgniter\Router\RouteCollection $routes */
$routes->group('blog', ['namespace' => 'App\Modules\Blog\Controllers'], static function ($routes) {
    $routes->get('/', 'PostController::index');
    $routes->get('(:segment)', 'PostController::show/$1');
});
```

> Esse arquivo será carregado **após** o global `app/Config/Routes.php`.

---

## 🤝 Contribuindo

1. _Fork_ → crie sua _branch_ (`git checkout -b feature/minha‑feature`)  
2. _Commit_ suas alterações (`git commit -m 'feat: nova feature'`)  
3. _Push_ (`git push origin feature/minha‑feature`) e abra um **Pull Request**.

---

## 🪪 Licença

Distribuído sob a licença **MIT**. Consulte `LICENSE` para detalhes.

---

## 📬 Dúvidas?

Abra uma **Issue** ou escreva para [ci4modules@rah.pt](mailto:ci4modules@rah.pt).
