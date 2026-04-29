/**
 * API.js - Classe centralizada para chamadas à API
 * Simplifica requisições HTTP para o backend
 */

class API {
    // URL base do backend
    static BASE = '../beckend';
    
    /**
     * Fazer requisição HTTP genérica
     */
    static async requisicao(endpoint, opcoes = {}) {
        const url = `${this.BASE}/${endpoint}`;
        const config = {
            headers: opcoes.headers || {},
            ...opcoes
        };
        
        try {
            const resposta = await fetch(url, config);
            const dados = await resposta.json();
            
            if (!resposta.ok && !dados.ok) {
                throw new Erro(dados.erro || 'Erro na requisição', resposta.status, dados);
            }
            
            return dados;
        } catch (erro) {
            if (erro instanceof Erro) throw erro;
            throw new Erro('Erro de conexão com o servidor', 0, erro);
        }
    }
    
    /**
     * Fazer requisição POST com JSON
     */
    static async post(endpoint, dados) {
        return this.requisicao(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dados)
        });
    }
    
    /**
     * Fazer requisição GET
     */
    static async get(endpoint) {
        return this.requisicao(endpoint, {
            method: 'GET'
        });
    }
    
    /**
     * Fazer requisição com FormData (para upload de arquivo)
     */
    static async postFormData(endpoint, formData) {
        return this.requisicao(endpoint, {
            method: 'POST',
            body: formData
        });
    }
    
    // =========================================
    // AUTENTICAÇÃO
    // =========================================
    
    /**
     * Fazer login
     */
    static async login(email, senha) {
        return this.post('login.php', { email, senha });
    }
    
    /**
     * Fazer logout
     */
    static async logout() {
        return this.get('logout.php');
    }
    
    /**
     * Verificar se usuário está autenticado
     */
    static async verificarSessao() {
        return this.get('sessao.php');
    }
    
    // =========================================
    // QUESTÕES - LISTAGEM E BUSCA
    // =========================================
    
    /**
     * Listar todas as questões com filtros opcionais
     */
    static async listarQuestoes(busca = '') {
        let endpoint = 'listar_questoes.php';
        
        if (busca) {
            endpoint += `?busca=${encodeURIComponent(busca)}`;
        }
        
        return this.get(endpoint);
    }
    
    /**
     * Buscar questão específica por ID
     */
    static async buscarQuestao(id) {
        return this.get(`buscar_questao.php?id=${encodeURIComponent(id)}`);
    }
    
    /**
     * Buscar questões por termo (título ou enunciado)
     */
    static async buscarQuestoes(termo) {
        return this.listarQuestoes(termo);
    }
    
    // =========================================
    // QUESTÕES - CRIAR E EDITAR
    // =========================================
    
    /**
     * Salvar nova questão ou editar existente
     * Aceita FormData para suportar upload de imagem
     */
    static async salvarQuestao(formData) {
        return this.postFormData('salvar_questao.php', formData);
    }
    
    /**
     * Criar questão objetiva
     */
    static async criarQuestaoObjetiva(dados) {
        const formData = this.construirFormDataQuestao({
            ...dados,
            tipo: 'objetiva'
        });
        
        return this.salvarQuestao(formData);
    }
    
    /**
     * Criar questão dissertativa
     */
    static async criarQuestaoDissertativa(dados) {
        const formData = this.construirFormDataQuestao({
            ...dados,
            tipo: 'dissertativa'
        });
        
        return this.salvarQuestao(formData);
    }
    
    /**
     * Editar questão existente
     */
    static async editarQuestao(id, dados) {
        const formData = this.construirFormDataQuestao({
            ...dados,
            id
        });
        
        return this.salvarQuestao(formData);
    }
    
    /**
     * Auxiliar para construir FormData de questão
     */
    static construirFormDataQuestao(dados) {
        const formData = new FormData();
        
        // Campos obrigatórios
        formData.append('id', dados.id || '');
        formData.append('tipo', dados.tipo || 'objetiva');
        formData.append('titulo', dados.titulo || '');
        formData.append('genero', dados.genero || '');
        formData.append('enunciado', dados.enunciado || '');
        formData.append('explicacao', dados.explicacao || '');
        formData.append('especificacao', dados.especificacao || '');
        formData.append('subgenero', dados.subgenero || '');
        formData.append('status', dados.status || 'rascunho');
        formData.append('acao', dados.acao || 'salvar');
        
        // Imagem
        if (dados.imagem instanceof File) {
            formData.append('imagem', dados.imagem);
        } else if (dados.imagem_atual) {
            formData.append('imagem_atual', dados.imagem_atual);
        }
        
        // Alternativas (para questões objetivas)
        if (dados.tipo === 'objetiva') {
            formData.append('correta', dados.correta || '');
            formData.append('alt_A', dados.alt_A || '');
            formData.append('alt_B', dados.alt_B || '');
            formData.append('alt_C', dados.alt_C || '');
            formData.append('alt_D', dados.alt_D || '');
            formData.append('alt_E', dados.alt_E || '');
        }
        
        return formData;
    }
    
    // =========================================
    // QUESTÕES - DELETAR
    // =========================================
    
    /**
     * Deletar questão por ID
     */
    static async excluirQuestao(id) {
        return this.post('excluir_questao.php', { id });
    }
    
    /**
     * Deletar múltiplas questões
     */
    static async excluirQuestoes(ids) {
        const requests = ids.map(id => this.excluirQuestao(id));
        return Promise.all(requests);
    }
}

// =========================================
// CLASSE: Erro Customizada
// =========================================

class Erro extends Error {
    constructor(mensagem, codigo = 0, detalhes = {}) {
        super(mensagem);
        this.nome = 'ErroAPI';
        this.codigo = codigo;
        this.detalhes = detalhes;
    }
    
    toString() {
        return `${this.nome}: ${this.message} (Código: ${this.codigo})`;
    }
}

// =========================================
// UTILITÁRIOS DE UI
// =========================================

/**
 * Mostrar mensagem de sucesso
 */
function mostrarSucesso(mensagem, elemento = null) {
    const el = elemento || document.querySelector('.aviso') || criarAlerta();
    el.textContent = mensagem;
    el.className = 'aviso sucesso show';
    
    setTimeout(() => {
        el.classList.remove('show');
    }, 4000);
}

/**
 * Mostrar mensagem de erro
 */
function mostrarErro(mensagem, elemento = null) {
    const el = elemento || document.querySelector('.aviso') || criarAlerta();
    el.textContent = mensagem;
    el.className = 'aviso erro show';
    
    setTimeout(() => {
        el.classList.remove('show');
    }, 5000);
}

/**
 * Mostrar aviso de exclusão
 */
function mostrarExclusao(mensagem, elemento = null) {
    const el = elemento || document.querySelector('.aviso') || criarAlerta();
    el.textContent = mensagem;
    el.className = 'aviso excluida show';
    
    setTimeout(() => {
        el.classList.remove('show');
    }, 3000);
}

/**
 * Criar elemento de alerta se não existir
 */
function criarAlerta() {
    const alerta = document.createElement('div');
    alerta.className = 'aviso';
    const main = document.querySelector('main') || document.body;
    main.insertBefore(alerta, main.firstChild);
    return alerta;
}

/**
 * Recarregar página com delay
 */
function recarregarPagina(delay = 1500) {
    setTimeout(() => {
        window.location.reload();
    }, delay);
}

/**
 * Redirecionar para página
 */
function redirecionarPara(pagina, delay = 1000) {
    setTimeout(() => {
        window.location.href = pagina;
    }, delay);
}

/**
 * Obter parâmetro de URL
 */
function obterParametrURL(nome) {
    const url = new URLSearchParams(window.location.search);
    return url.get(nome);
}

/**
 * Validar email
 */
function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Limpar formulário
 */
function limparFormulario(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.reset();
    }
}

/**
 * Desabilitar/habilitar botão
 */
function desabilitarBotao(botao, desabilitar = true) {
    if (typeof botao === 'string') {
        botao = document.getElementById(botao);
    }
    if (botao) {
        botao.disabled = desabilitar;
        botao.style.opacity = desabilitar ? '0.6' : '1';
    }
}

/**
 * Mostrar/ocultar elemento
 */
function mostrarElemento(elemento, mostrar = true) {
    if (typeof elemento === 'string') {
        elemento = document.getElementById(elemento);
    }
    if (elemento) {
        elemento.style.display = mostrar ? 'block' : 'none';
    }
}

/**
 * Confirmar ação com dialog
 */
function confirmarAcao(mensagem = 'Tem certeza?') {
    return confirm(mensagem);
}

/**
 * Formatar data para exibição
 */
function formatarData(data, formato = 'BR') {
    if (typeof data === 'string') {
        data = new Date(data);
    }
    
    if (formato === 'BR') {
        return data.toLocaleDateString('pt-BR');
    }
    
    return data.toLocaleDateString();
}

/**
 * Converter arquivo para Base64 (para pré-visualização)
 */
function lerArquivoComoBase64(arquivo) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(arquivo);
    });
}

/**
 * Pré-visualizar imagem antes de fazer upload
 */
async function previsualizarImagem(inputFile, imagemElement) {
    const arquivo = inputFile.files[0];
    
    if (!arquivo) return;
    
    try {
        const base64 = await lerArquivoComoBase64(arquivo);
        imagemElement.src = base64;
        imagemElement.style.display = 'block';
    } catch (erro) {
        console.error('Erro ao ler imagem:', erro);
    }
}
