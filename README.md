# Unifilmes - Sistema de Cadastro de Filmes e Usuários

Este projeto é uma aplicação web desenvolvida em PHP, com MySQL, que permite:
- Cadastro, edição, exclusão e pesquisa de usuários (com validação de CPF e senha forte)
- Cadastro, edição, exclusão e pesquisa de filmes
- Autenticação de usuários por CPF e senha
- Integração com a API TMDB para exibir filmes em destaque e busca de filmes

## Estrutura do Projeto

```
php_estudos/
├── database/
│   └── usuarios.sql         # Script SQL para criar a tabela de usuários
├── src/
│   ├── autenticacao.php     # Verifica se o usuário está logado
│   ├── conexao.php          # Conexão com o banco de dados MySQL
│   ├── description.php      # Detalhes do filme (API TMDB)
│   ├── index.php            # Tela de login
│   ├── login.php            # Processa o login
│   ├── logout.php           # Logout do usuário
│   ├── movies.php           # CRUD de filmes
│   ├── principal.php        # Página principal (filmes em destaque e busca TMDB)
│   ├── register.php         # Cadastro de novos usuários
│   ├── tmdb_api.php         # Integração com a API TMDB
│   └── usuarios.php         # CRUD de usuários
├── css/
│   ├── login.css            # Estilos para login/cadastro
│   ├── modal.css            # Estilos dos modais
│   └── style.css            # Estilos gerais do sistema
├── scripts/
│   ├── dropdown.js          # Script do menu dropdown
│   └── openEditModal.js     # Script dos modais de edição/usuário/filme
└── README.md                # Documentação do projeto
```

## Funcionalidades

- **Cadastro e autenticação de usuários** (com validação de CPF e senha forte)
- **CRUD de usuários** (criar, editar, excluir, pesquisar)
- **CRUD de filmes** (criar, editar, excluir, pesquisar)
- **Busca e exibição de filmes populares da API TMDB**
- **Página de detalhes do filme** (capa, trailer, sinopse, etc)
- **Interface moderna e responsiva**

## Validações
- **CPF**: Máscara e validação matemática (backend e frontend)
- **Senha**: Mínimo 6 caracteres, 1 maiúscula, 1 minúscula, 1 número e 1 especial (backend e frontend)

## Como Usar

1. Execute o script `database/usuarios.sql` no seu MySQL para criar a tabela de usuários.
2. Configure o acesso ao banco em `src/conexao.php`.
3. Inicie o servidor (ex: XAMPP) e acesse `http://localhost/php_estudos/src/index.php`.

## Contribuição

Sinta-se à vontade para sugerir melhorias ou reportar problemas.