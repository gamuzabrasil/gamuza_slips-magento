<?php
// +----------------------------------------------------------------------+
// | BoletoPhp - Versão Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo está disponível sob a Licença GPL disponível pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Você deve ter recebido uma cópia da GNU Public License junto com     |
// | esse pacote; se não, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colaborações de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de João Prado Maia e Pablo Martins F. Costa			       	  |
// | 																	                                    |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordenação Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto Bradesco: Ramon Soares						            |
// +----------------------------------------------------------------------+

header ('Content-Type: text/html; Charset=UTF-8');

$identification = 'Gamuza Slips - Baseado no projeto BoletoPHP.';
$analytics = <<<ANALYTICS
<script type="text/javascript">
</script>
ANALYTICS;

// ------------------------- DADOS DINÂMICOS DO SEU CLIENTE PARA A GERAÇÃO DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formulário c/ POST, GET ou de BD (MySql,Postgre,etc)	//

// DADOS DO BOLETO PARA O SEU CLIENTE
//$dias_de_prazo_para_pagamento = 5;
$taxa_boleto = $_POST ['tax']; // 2.95;
//$data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006"; 
$data_venc = date ("d/m/Y", strtotime ($_POST ['expiration']));
$valor_cobrado = $_POST ['amount']; // "2950,00"; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
$valor_cobrado = str_replace(",", ".",$valor_cobrado);
$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

$dadosboleto["nosso_numero"] = $_POST ['number']; // "75896452";  // Nosso numero sem o DV - REGRA: Máximo de 11 caracteres!
$dadosboleto["numero_documento"] = $_POST ['order_id']; // $dadosboleto["nosso_numero"];	// Num do pedido ou do documento = Nosso numero
$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto
$dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = $_POST ['customer_name']; // "Nome do seu Cliente";
$dadosboleto["endereco1"] = $_POST ['customer_address']; // "Endereço do seu Cliente";
$dadosboleto["endereco2"] = $_POST ['customer_city_region_postcode']; // "Cidade - Estado -  CEP: 00000-000";

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = $_POST ['demonstrative1']; // "Pagamento de Compra na Loja Nonononono";
$dadosboleto["demonstrativo2"] = $_POST ['demonstrative2']; // "Mensalidade referente a nonon nonooon nononon<br>Taxa bancária - R$ ".number_format($taxa_boleto, 2, ',', '');
$dadosboleto["demonstrativo3"] = $_POST ['demonstrative3']; // "BoletoPhp - http://www.boletophp.com.br";
$dadosboleto["instrucoes1"] = $_POST ['instructions1']; // "- Sr. Caixa, cobrar multa de 2% após o vencimento";
$dadosboleto["instrucoes2"] = $_POST ['instructions2']; // "- Receber até 10 dias após o vencimento";
$dadosboleto["instrucoes3"] = $_POST ['instructions3']; // "- Em caso de dúvidas entre em contato conosco: xxxx@xxxx.com.br";
$dadosboleto["instrucoes4"] = $_POST ['instructions4']; // "&nbsp; Emitido pelo sistema Projeto BoletoPhp - www.boletophp.com.br";

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = $_POST ['quantity']; // "001";
$dadosboleto["valor_unitario"] = $_POST ['unitary_value']; // $valor_boleto;
$dadosboleto["aceite"] = $_POST ['acceptance']; // "";		
$dadosboleto["especie"] = $_POST ['specie']; // "R$";
$dadosboleto["especie_doc"] = $_POST ['specie_doc']; // "DS";


// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //


// DADOS DA SUA CONTA - Bradesco
$dadosboleto["agencia"] = $_POST ['agency']; // "1172"; // Num da agencia, sem digito
$dadosboleto["agencia_dv"] = $_POST ['agency_dv']; // "0"; // Digito do Num da agencia
$dadosboleto["conta"] = $_POST ['account']; // "0403005"; 	// Num da conta, sem digito
$dadosboleto["conta_dv"] = $_POST ['account_dv']; // "2"; 	// Digito do Num da conta

// DADOS PERSONALIZADOS - Bradesco
$dadosboleto["conta_cedente"] = $_POST ['transferor_account']; // "0403005"; // ContaCedente do Cliente, sem digito (Somente Números)
$dadosboleto["conta_cedente_dv"] = $_POST ['transferor_account_dv']; // "2"; // Digito da ContaCedente do Cliente
$dadosboleto["carteira"] = $_POST ['portfolio']; // "06";  // Código da Carteira: pode ser 06 ou 03

// SEUS DADOS
$dadosboleto["identificacao"] = $identification; // "BoletoPhp - Código Aberto de Sistema de Boletos";
$dadosboleto["cpf_cnpj"] = $_POST ['company_taxvat']; // "";
$dadosboleto["endereco"] = $_POST ['company_address']; // "Coloque o endereço da sua empresa aqui";
$dadosboleto["cidade_uf"] = $_POST ['company_city_region']; // "Cidade / Estado";
$dadosboleto["cedente"] = $_POST ['company_name']; // "Coloque a Razão Social da sua empresa aqui";

// GAMUZA SLIP
$dadosboleto["endereco_logo_empresa"] = $_POST ['company_logo_url'];
//// $dadosboleto["analytics"] = $analytics;

// NÃO ALTERAR!
include("include/funcoes_bradesco.php"); 
include("include/layout_bradesco.php");
?>

