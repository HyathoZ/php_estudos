# Projeto PHP Estudo - Cadastro de Filmes

Este projeto é uma aplicação web simples desenvolvida em PHP que permite o cadastro e autenticação de usuários. Abaixo estão os detalhes dos arquivos e suas funcionalidades.

## Estrutura do Projeto

```
php_estudos
├── database
│   └── usuarios.sql          # Script SQL para criar a tabela de usuários e inserir dados iniciais.
├── src
│   ├── autenticacao.php      # Verifica se o usuário está logado e se a senha está definida.
│   ├── conexao.php           # Estabelece a conexão com o banco de dados MySQL.
│   ├── index.php             # Página de login com formulário para CPF e senha, e botão para cadastro.
│   ├── login.php             # Processa o login do usuário.
│   ├── logout.php            # Encerra a sessão do usuário.
│   ├── principal.php         # Página principal exibida após o login bem-sucedido.
│   └── register.php          # Tela de cadastro para novos usuários.
├── css
│   ├── login.css             # Estilos para a página de login.
│   └── style.css             # Estilos para a página principal e outras partes do sistema.
└── README.md                 # Documentação do projeto.
```

## Funcionalidades

- **Cadastro de Usuários**: Permite que novos usuários se cadastrem no sistema.
- **Login de Usuários**: Usuários podem fazer login utilizando CPF e senha.
- **Logout**: Usuários podem encerrar a sessão.
- **Autenticação**: Verifica se o usuário está logado antes de acessar páginas restritas.

## Como Usar

1. **Configuração do Banco de Dados**: Execute o script `usuarios.sql` no seu banco de dados MySQL para criar a tabela de usuários.
2. **Configuração do Servidor**: Certifique-se de que o servidor web (como XAMPP) esteja em execução.
3. **Acessar a Aplicação**: Navegue até `http://localhost/php_estudos/src/index.php` para acessar a página de login.

## Contribuições

Sinta-se à vontade para contribuir com melhorias ou correções.