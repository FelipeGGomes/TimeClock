# 🕒 TimeClock API (Sistema de Ponto Eletrônico)

API REST desenvolvida em **Laravel** para gerenciamento de registros de ponto, com validação de horários e sistema automatizado de notificação de inconsistências via e-mail.

## 🚀 Tecnologias Utilizadas

- **PHP 8.2+**
- **Laravel 10/11**
- **MySQL**
- **Sanctum** (Autenticação via Token)
- **Task Scheduling** (Automação de tarefas)
- **SMTP** (Envio de E-mails)

## ⚙️ Funcionalidades

- ✅ **CRUD de Pontos:** Registro de Entrada, Intervalo (Início/Fim) e Saída.
- 🔒 **Segurança:** O horário (`timestamp`) é gerado pelo servidor, impedindo fraudes de horário local.
- 🤖 **Auditoria Automática:** Um comando (`ponto:verificar`) roda diariamente para analisar os registros do dia anterior.
- 📧 **Notificação de Erro:** Caso o funcionário esqueça de bater o ponto (ex: saiu para almoçar e não marcou), o sistema envia um e-mail automático solicitando justificativa.

## 🛠️ Como rodar o projeto

### Pré-requisitos
Certifique-se de ter instalado: PHP, Composer e MySQL.

1. **Clone o repositório:**
   ```bash
   git clone [https://github.com/seu-usuario/timeclock-api.git](https://github.com/seu-usuario/timeclock-api.git)
   cd timeclock-api
   ```

2. **Instale as dependências:**
   ```bash
   composer install
   ```

3. **Configure o ambiente:**
   ```bash
    cp .env.example .env
    php artisan key:generate
   ```

4. **Banco de Dados:**
   ```bash
    php artisan migrate
   ```
5. **Rodando o servidor:**
   ```bash
   php artisan serve
   ```


## 🔌 Documentação da API

- **POST - /api/pontos	- Registra uma batida de ponto.**
- **GET - /api/pontos	- Lista o histórico do usuário logado.**
- **GET - /api/pontos/{id}	- Detalhes de um registro específico.**
- **PUT - /api/pontos/{id}	- Justificativa/Correção de ponto.**



## 🔌 Exemplo de JSON para Registro (POST):
```json
{
    "tipo": "entrada",
    "justificativa": "Ponto normal"
}
```


## 📧 ⏰ Agendamento (Task Scheduler)

O sistema utiliza o **Task Scheduler** do Laravel para rodar automaticamente a verificação de pontos diariamente.

- **Comando:** `php artisan ponto:verificar`
- **Horário:** Configurado para rodar às **08:00 AM** diariamente.

Certifique-se de que o comando está configurado no `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('ponto:verificar')->dailyAt('08:00');
}
```

### 2. Documentação Técnica (Resumo da Lógica)

Se alguém perguntar numa entrevista "Como você fez a lógica de verificação?", você pode explicar assim (ou salvar num arquivo `DOCS.txt`):

**Lógica de Verificação de Inconsistência:**
1.  **Agendamento:** O Laravel Schedule roda todo dia às 08:00.
2.  **Seleção:** O script seleciona todos os usuários ativos.
3.  **Filtragem:** Busca os registros (`TimeRecord`) do dia anterior (`yesterday`).
4.  **Validação de Pares:**
    * Se existe *Entrada*, deve existir *Saída*.
    * Se existe *Intervalo_Inicio*, deve existir *Intervalo_Fim*.
5.  **Ação:** Se faltar algum par, o sistema dispara um Mailable usando Filas (Queue/Sync) para o e-mail cadastrado do usuário.

---

## 📬 Configuração de E-mail


Para o envio de e-mails, configure as variáveis no arquivo `.env`:

```