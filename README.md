# Sistema de Gerenciamento de Biblioteca

Um sistema moderno e completo para gerenciamento de biblioteca, desenvolvido com Laravel 10 e Vue.js 3.

## 🚀 Funcionalidades

### 📚 Gerenciamento de Livros
- Cadastro completo de livros com validação
- Campos: título, descrição, ISBN, ano de publicação, páginas, idioma
- Vinculação com autores e editoras
- Busca avançada
- Formatação automática de ISBN

### ✍️ Gerenciamento de Autores
- Cadastro de autores com validação
- Campos: nome, email, telefone
- Listagem com busca em tempo real
- Formatação automática de telefone

### 🏢 Gerenciamento de Editoras
- Cadastro completo de editoras
- Campos: nome, email, telefone, endereço
- Sistema de busca integrado
- Validação de dados

### 🔐 Sistema de Autenticação
- Login seguro com JWT
- Proteção de rotas
- Gerenciamento de sessão
- Validação de formulários

## 🛠️ Tecnologias Utilizadas

### Backend
- Laravel 10
- PHP 8
- MySQL
- JWT Authentication
- API RESTful

### Frontend
- Vue.js 3
- Composition API
- Axios para requisições HTTP
- Vue Router para navegação
- CSS moderno e responsivo

## 📋 Pré-requisitos

- PHP >= 8.1
- Composer
- Node.js >= 16
- MySQL

## 🔧 Instalação

1. Clone o repositório
```bash
git clone https://github.com/CloneX2013/sistemabibliotecalaravelvue.git
```

2. Instale as dependências do Laravel
```bash
cd projetovue
composer install
```

3. Configure o arquivo .env
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure o banco de dados no arquivo .env
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=seu_banco_de_dados
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

5. Execute as migrações
```bash
php artisan migrate
```

6. Instale as dependências do Vue.js
```bash
cd ../frontend
npm install
```

## 🚀 Executando o projeto

1. Inicie o servidor Laravel
```bash
cd projetovue
php artisan serve
```

2. Em outro terminal, inicie o servidor Vue.js
```bash
cd frontend
npm run dev
```

3. Acesse o sistema em `http://localhost:5173`

## 📱 Interfaces

### Tela de Login
- Interface moderna e intuitiva
- Validação em tempo real
- Feedback visual para o usuário

### Dashboard Principal
- Menu lateral de navegação
- Área principal dinâmica
- Sistema de busca integrado

### Formulários
- Validação em tempo real
- Feedback visual
- Formatação automática de campos especiais (telefone, ISBN)
- Modal para ações de criar/editar

## 🔒 Segurança
- Autenticação JWT
- Validação de dados no frontend e backend
- Proteção contra CSRF
- Sanitização de inputs

## 🔄 API Endpoints

### Autenticação
- POST `/api/login` - Login de usuário
- POST `/api/logout` - Logout de usuário

### Autores
- GET `/api/authors` - Lista todos os autores
- POST `/api/authors` - Cria novo autor
- PUT `/api/authors/{id}` - Atualiza autor
- DELETE `/api/authors/{id}` - Remove autor

### Editoras
- GET `/api/publishers` - Lista todas as editoras
- POST `/api/publishers` - Cria nova editora
- PUT `/api/publishers/{id}` - Atualiza editora
- DELETE `/api/publishers/{id}` - Remove editora

### Livros
- GET `/api/books` - Lista todos os livros
- POST `/api/books` - Cria novo livro
- PUT `/api/books/{id}` - Atualiza livro
- DELETE `/api/books/{id}` - Remove livro

## 🔜 Próximos Passos
- Sistema de empréstimos
- Upload de imagens para livros
- Dashboard com estatísticas
- Categorização de livros
- Sistema de reservas

## 👥 Contribuição
Contribuições são sempre bem-vindas! Para contribuir:

1. Faça um Fork do projeto
2. Crie uma Branch para sua Feature (`git checkout -b feature/AmazingFeature`)
3. Faça o Commit de suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Faça o Push para a Branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📝 Licença
Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## ✨ Agradecimentos
- Equipe de desenvolvimento
- Comunidade Laravel e Vue.js
- Todos os contribuidores

---
Desenvolvido com ❤️ por Pedro Paulo.
