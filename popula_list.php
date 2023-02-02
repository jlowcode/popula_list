<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

// Require the abstract plugin class
require_once COM_FABRIK_FRONTEND . '/models/plugin-list.php';

class PlgFabrik_ListPopula_List extends PlgFabrik_List
{
    private $group_id = 0;
    private $list_id = 0;
    private $form_id = 0;
    # joomla_fabrik_elements
    public function onLoadData()
    {
        $model = $this->getModel();
        $params = $this->getParams();
        $tableName = $model->getTable()->db_table_name;
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $columns = $db->getTableColumns($tableName);
        $this->form_id = $model->getTable()->form_id;
        $this->list_id = $model->getTable()->id;
        $this->group_id = $this->getGroupId($this->form_id);
        
        if ($user->authorise('core.admin')) {
            $number = $params->get('pivot_round_to');
            $ignores = ["id", "user", "ip"];
            foreach ($ignores as $i) {
                unset($columns[$i]);
            }
            $this->gravarDados($this->getDados(intval($number), $columns), $tableName);
            echo "<h4 style='color:red'>Novos $number Registros Criados!</h4>";
        } else {
            return;
        }
    }

    public function gravarDados($dados, $tableName)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        foreach ($dados as $k => $x) {
            $collumns = array_keys($x);
            $query
                ->clear()
                ->insert($db->quoteName($tableName))
                ->columns($db->quoteName($collumns))
                ->values(implode(",", $x));
            $db->setQuery($query);
            $db->execute();
        }
    }
    public function getDados($number, $columns = [])
    {
        $nomesPopularesBrasil = array(
            "Lucas", "Larissa", "Matheus", "Julia", "Guilherme", "Maria", "Rafael", "Gabriela", "Pedro", "Isabela",
            "Felipe", "Sophia", "João", "Ana", "Samuel", "Beatriz", "Diego", "Luana", "Nicolas", "Laura",
            "Vinicius", "Emanuella", "Henrique", "Valentina", "Leonardo", "Lorena", "Arthur", "Rafaela", "Thiago", "Mariana",
            "Gustavo", "Júlia", "Caleb", "Lívia", "Carlos", "Kelly", "Eduardo", "Letícia", "Bruno", "Laís",
            "Heitor", "Alícia", "Davi", "Fernanda", "Enzo", "Lívia", "Joaquim", "Bianca", "Miguel", "Manuela",
            "Gabriel", "Rute", "Lorenzo", "Isadora", "Pedro Henrique", "Mirella", "Luiz", "Analu", "Igor", "Lara", "Lucas", "Larissa", "Matheus", "Julia", "Guilherme", "Maria", "Rafael", "Gabriela", "Pedro", "Isabela",
            "Felipe", "Sophia", "João", "Ana", "Samuel", "Beatriz", "Diego", "Luana", "Nicolas", "Laura",
            "Vinicius", "Emanuella", "Henrique", "Valentina", "Leonardo", "Lorena", "Arthur", "Rafaela", "Thiago", "Mariana",
            "Gustavo", "Júlia", "Caleb", "Lívia", "Carlos", "Kelly", "Eduardo", "Letícia", "Bruno", "Laís",
            "Heitor", "Alícia", "Davi", "Fernanda", "Enzo", "Lívia", "Joaquim", "Bianca", "Miguel", "Manuela",
            "Gabriel", "Rute", "Lorenzo", "Isadora", "Pedro Henrique", "Mirella", "Luiz", "Analu", "Igor", "Lara",
            "William", "Camila", "Erick", "Larissa", "Victor", "Amanda", "Fernando", "Juliana", "Santiago", "Bruna",
            "Luciano", "Isabel", "Leandro", "Alessandra", "Breno", "Leticia", "Otávio", "Jaqueline", "Max", "Debora",
            "Yuri", "Carolina", "Claudio", "Vitória", "Rodrigo", "Evelyn", "Ricardo", "Rayssa", "Tiago", "Luana",
            "Andre", "Mirella", "Cristiano", "Lorena", "Marcelo", "Luana", "Alex", "Aline", "Elias", "Gabriela",
            "Paulo", "Laryssa", "Gustavo", "Caroline", "Renato", "Larissa", "Geraldo", "Priscila", "Igor", "Larissa",
            "João Paulo", "Raissa", "José", "Ana Luiza", "Erick", "Lívia", "Alessandro", "Lavínia", "Roger", "Ana Paula",
            "Reginaldo", "Mariana", "Diogo", "Larissa", "Fábio", "Laryssa", "Sérgio", "Luciana", "Sandro", "Adriana",
            "Leonardo", "Thais", "Marcel", "Larissa", "Roberto", "Lilian", "Cláudio", "Larissa", "Osvaldo", "Larissa",
            "Julio", "Larissa", "Rafael", "Larissa", "Ezequiel", "Larissa", "Lucas", "Larissa", "Lucas", "Larissa",
            "Lucas", "Larissa", "Lucas", "Larissa", "Lucas"
        );

        $sobrenomes = array(
            "Santos", "Silva", "Oliveira", "Souza", "Costa", "Rodrigues", "Almeida", "Pereira", "Martins", "Lima",
            "Jesus", "Araújo", "Gomes", "Gonçalves", "Ferreira", "Dias", "Castro", "Barbosa", "Cardoso", "Lopes",
            "Fernandes", "Correia", "Ribeiro", "Martinez", "Ramos", "Silveira", "Ramalho", "Siqueira", "Mendes", "Machado",
            "Freitas", "Azevedo", "Rosa", "Fonseca", "Nascimento", "Teixeira", "Pinto", "Baptista", "Moreira", "Vieira",
            "Alves", "Moura", "Cunha", "Campos", "Borges", "Moraes", "Andrade", "Sá", "Coelho", "Vaz",
            "Cavalcanti", "Nogueira", "Rios", "Castro", "Novaes", "Braga", "Carvalho", "Monteiro", "Peixoto", "Couto",
            "Bezerra", "Barros", "Medeiros", "Guerra", "Faria", "Coutinho", "Guimarães", "Leite", "Melo", "Brito",
            "Paes", "Franca", "Pessoa", "Cavalieri", "Nery", "Amorim", "Viana", "Santana", "Matos", "Souza",
            "Cordeiro", "Varela", "Nunes", "Rezende", "Figueiredo", "Carneiro", "Guedes", "Garcia", "Mendona", "Sousa",
            "Pereira", "Geraldo", "Rocha", "Leal", "Viana", "Magalhães", "Morais", "Nascimento", "Cabral", "Ribeiro",
            "Alencar", "Menezes", "Esteves", "Marques", "Silveira", "Alberto", "Oliveira", "Paz", "Coutinho", "Souza",
            "Souza", "Santana", "Aguiar", "Batista", "Vargas", "Sousa", "Gonçalves", "Ribeiro", "Cunha", "Barros",
            "Cardoso", "Oliveira", "Gomes", "Silva", "Peixoto", "Martins", "Lima", "Couto", "Rosa", "Teixeira",
            "Martinez", "Lopes", "Martins", "Correia", "Monteiro", "Castro", "Garcia", "Cavalcanti", "Moura", "Pessoa"
        );

        $cidades = array(
            "São Paulo", "Rio de Janeiro", "Belo Horizonte", "Brasília", "Salvador", "Fortaleza", "Manaus", "Curitiba", "Recife", "Belém",
            "Goiânia", "Guarulhos", "Campinas", "São Gonçalo", "Maceió", "Duque de Caxias", "Natal", "Teresina", "São Luís",
            "João Pessoa", "São Bernardo do Campo", "Santo André", "Osasco", "Ribeirão Preto", "Uberlândia", "Contagem",
            "Sorocaba", "Nova Iguaçu", "Campo Grande", "Cuiabá", "Mauá", "Feira de Santana", "Joinville", "Florianópolis",
            "Ananindeua", "São José dos Campos", "Jaboatão dos Guararapes", "Rio Branco", "Aparecida de Goiânia", "Mogi das Cruzes",
            "São João de Meriti", "Betim", "Caxias do Sul", "Porto Alegre", "Cariacica", "Serra", "Niterói", "Vila Velha",
            "Praia Grande", "Vitória", "Canoas", "Foz do Iguaçu", "Maringá", "São José do Rio Preto", "Camaçari", "Santos",
            "São Vicente", "Londrina", "Uberaba", "Diadema", "Jundiaí", "Piracicaba", "Paulista", "Carioba", "Itaquaquecetuba",
            "Blumenau", "Franca", "Sumaré", "Itajaí", "Rio Grande", "Jaú", "Americana", "Bauru", "Suzano", "Indaiatuba",
            "Cotia", "Taboão da Serra", "Santa Maria", "Embu das Artes", "Itapetininga", "Itapevi", "Limeira", "Guarujá",
            "Osório", "Alvorada", "Barueri", "Araraquara", " Jacareí", "Taubaté", "Rio Claro", "Cachoeiro de Itapemirim",
            "Ilha Solteira", "Ourinhos", "São Carlos", "Avaré", "Rio Verde", "Ituiutaba", "Itu", "Valinhos", "Jales",
            "Cotia", "Araçatuba", "Assis", "Barra Mansa", "Jacutinga", "Conselheiro Lafaiete", "Bento Gonçalves",
            "Rio das Ostras", "Mesquita", "Corumbá", "Itapira", "Resende"
        );

        $telefones = array();
        for ($i = 1; $i <= 100; $i++) {
            $telefone = "(" . rand(10, 99) . ") " . rand(1000, 9999) . "-" . rand(1000, 9999);
            array_push($telefones, $telefone);
        }

        $empresas = array(
            "Petrobras",
            "Vale",
            "Itaú Unibanco",
            "Bradesco",
            "Banco do Brasil",
            "Ambev",
            "JBS",
            "Gol Linhas Aéreas",
            "TIM Participações",
            "Iguatemi Empresas de Shopping Centers",
            "Embraer",
            "Braskem",
            "MRV Engenharia e Participações",
            "Cemig",
            "Raia Drogasil",
            "C&A Modas",
            "Lojas Americanas",
            "Cosan",
            "Multiplan Empreendimentos Imobiliários",
            "Locaweb",
            "Usiminas",
            "Gerdau",
            "Eletrobras",
            "Weg",
            "Copel",
            "CVC Brasil Operadora e Agência de Viagens",
            "Souza Cruz",
            "Marcopolo",
            "Fibria Celulose",
            "CPFL Energia",
            "Sanepar",
            "BRF",
            "Tecnisa",
            "Light S.A.",
            "Porto Seguro",
            "Valeo TCS",
            "Schneider Electric Brasil",
            "Tereos Açúcar e Etanol",
            "CPFL Renováveis",
            "Ecorodovias Infraestrutura e Logística",
            "Estácio Participações",
            "Cielo",
            "Vivo Participações",
            "Oi",
            "Suzano Papel e Celulose",
            "Mercado Livre"
        );
        $listaDados = [];
        while ($number > 0) {
            $number--;
            foreach ($columns as $key => $type) {
                $dabasejoin = $this->verificaDatabaseJoin($key);
                if ($dabasejoin) {
                    $item[$key] =  $dabasejoin;
                } else {
                    switch ($key) {
                        case 'nome':
                            $item[$key] = "'" . $nomesPopularesBrasil[array_rand($nomesPopularesBrasil)] . "'";
                            break;
                        case 'sobrenome':
                            $item[$key] = "'" . $sobrenomes[array_rand($sobrenomes)] . "'";
                            break;
                        case 'cidade':
                            $item[$key] = "'" . $cidades[array_rand($cidades)] . "'";
                            break;
                        case 'telefone':
                            $item[$key] = "'" . $telefones[array_rand($telefones)] . "'";
                            break;
                        case 'empresa':
                            $item[$key] = "'" . $empresas[array_rand($empresas)] . "'";
                            break;
                        default:
                            switch ($type) {
                                case 'int':
                                    $item[$key] =  $number;
                                    break;
                                case 'varchar':
                                    $item[$key] = "'" . $key . "_" . $number . "'";
                                    break;
                                case 'datetime':
                                    $item[$key] = "NOW()";
                                    break;
                            }
                    }
                }
            }
            array_push($listaDados, $item);
        }
        return $listaDados;
    }
    public function geraCPF($comPontos = false)
    {
        $n1 = rand(0, 9);
        $n2 = rand(0, 9);
        $n3 = rand(0, 9);
        $n4 = rand(0, 9);
        $n5 = rand(0, 9);
        $n6 = rand(0, 9);
        $n7 = rand(0, 9);
        $n8 = rand(0, 9);
        $n9 = rand(0, 9);
        $d1 = $n9 * 2 + $n8 * 3 + $n7 * 4 + $n6 * 5 + $n5 * 6 + $n4 * 7 + $n3 * 8 + $n2 * 9 + $n1 * 10;
        $d1 = 11 - ($this->mod($d1, 11));
        if ($d1 >= 10) {
            $d1 = 0;
        }
        $d2 = $d1 * 2 + $n9 * 3 + $n8 * 4 + $n7 * 5 + $n6 * 6 + $n5 * 7 + $n4 * 8 + $n3 * 9 + $n2 * 10 + $n1 * 11;
        $d2 = 11 - ($this->mod($d2, 11));
        if ($d2 >= 10) {
            $d2 = 0;
        }
        $retorno = '';
        if ($comPontos) {
            $retorno = '' . $n1 . $n2 . $n3 . "." . $n4 . $n5 . $n6 . "." . $n7 . $n8 . $n9 . "-" . $d1 . $d2;
        } else {
            $retorno = '' . $n1 . $n2 . $n3 . $n4 . $n5 . $n6 . $n7 . $n8 . $n9 . $d1 . $d2;
        }
        return $retorno;
    }
    public function mod($dividendo, $divisor)
    {
        return round($dividendo - (floor($dividendo / $divisor) * $divisor));
    }
    private function verificaDatabaseJoin($coluna)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from($db->quoteName('joomla_fabrik_elements'));
        $query->where($db->quoteName('name') . ' = ' . $db->quote($coluna));
        $query->where($db->quoteName('group_id') . ' = ' . $db->quote($this->group_id));
        $db->setQuery($query);
        $result = $db->loadObjectList()[0];
        if ($result->plugin != 'databasejoin') {
            return false;
        }
        $params = json_decode($result->params);
        if (!is_object($params)) {
            return false;
        }
        $tableJoin = $params->join_db_name;
        $colunaJoin = $params->join_key_column;
        $query = $db->getQuery(true);
        $query->select('*')->from($db->quoteName($tableJoin));
        $query->order('RAND()');
        $query->setLimit(1);
        $db->setQuery($query);
        $result2 = $db->loadObjectList()[0];
        return $result2->$colunaJoin;
    }
    private function getGroupId($form_id)
    {

        // $listModel = JModelLegacy::getInstance('List', 'FabrikFEModel');
        // $listModel->setId($this->list_id); //Aqui precisa setar o id da lista q vc esta trabalhando
        // $formModel = $listModel->getFormModel();
        // $groupModel = $formModel->getGroupsHiarachy();
        // $properts = $groupModel->getGroup()->getProperties();  //interessante fazer um foreach pq pode haver varios groups para uma lista
        // $idGroup = $properts->id;

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from($db->quoteName('joomla_fabrik_formgroup'));
        $query->where($db->quoteName('form_id') . ' = ' . $db->quote($form_id));
        $db->setQuery($query);
        $result = $db->loadObjectList()[0];
        return $result->group_id ?? false;
    }
    // Método para Criar Tabelas Teste
    public function createDatabase()
    {
        // echo "<script>console.log('Metodo Chamado')</script>";
        $tabelaExists = false;
        $db = JFactory::getDbo();
        // Criação da tabela joomla_popula_empresas
        if (!$db->tableExists('joomla_popula_empresas')) {
            echo "<script>console.log('Não Existe a Tabela joomla_popula_empresas')</script>";
            $query = "CREATE TABLE IF NOT EXISTS `joomla_popula_empresas` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `razao_social` varchar(255) NOT NULL,
            `telefone` varchar(255) NOT NULL,
            `data_criacao` date NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            $db->setQuery($query);
            $db->execute();
            $empresas = array(
                "Petrobras",
                "Vale",
                "Itaú Unibanco",
                "Bradesco",
                "Banco do Brasil",
                "Ambev",
                "JBS",
                "Gol Linhas Aéreas",
                "TIM Participações",
                "Iguatemi Empresas de Shopping Centers",
                "Embraer",
                "Braskem",
                "MRV Engenharia e Participações",
                "Cemig",
                "Raia Drogasil",
                "C&A Modas",
                "Lojas Americanas",
                "Cosan",
                "Multiplan Empreendimentos Imobiliários",
                "Locaweb",
                "Usiminas",
                "Gerdau",
                "Eletrobras",
                "Weg",
                "Copel",
                "Rede D'Or São Luiz",
                "CVC Brasil Operadora e Agência de Viagens",
                "Souza Cruz",
                "Marcopolo",
                "Fibria Celulose",
                "CPFL Energia",
                "Sanepar",
                "BRF",
                "Tecnisa",
                "Light S.A.",
                "Porto Seguro",
                "Valeo TCS",
                "Schneider Electric Brasil",
                "Tereos Açúcar e Etanol",
                "CPFL Renováveis",
                "Ecorodovias Infraestrutura e Logística",
                "Estácio Participações",
                "Cielo",
                "Vivo Participações",
                "Oi",
                "Suzano Papel e Celulose",
                "Mercado Livre"
            );
            $query = $db->getQuery(true);
            foreach ($empresas as $empresa) {
                $columns = array('razao_social', 'telefone', 'data_criacao');
                $values = array($db->quote($empresa), 1, $db->quote(date('Y-m-d H:i:s')));
                $query
                    ->clear()
                    ->insert($db->quoteName('joomla_popula_empresas'))
                    ->columns($db->quoteName($columns))
                    ->values(implode(',', $values));

                $db->setQuery($query);
                $db->execute();
            }
        }
        // Criação da tabela joomla_popula_cliente
        if (!$db->tableExists('joomla_popula_cliente')) {
            echo "<script>console.log('Não Existe a Tabela joomla_popula_cliente')</script>";
            $query = "CREATE TABLE IF NOT EXISTS `joomla_popula_cliente` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `nome` varchar(255) NOT NULL,
                `sobrenome` varchar(255) NOT NULL,
                `cidade` varchar(255) NOT NULL,
                `telefone` varchar(255) NOT NULL,
                `empresa` int(11) NOT NULL,
                PRIMARY KEY (`id`),
                FOREIGN KEY (`empresa`) REFERENCES `joomla_popula_empresas` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            $db->setQuery($query);
            $db->execute();
        }
    }
}
