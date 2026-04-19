/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.22-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: intranet_acqua
-- ------------------------------------------------------
-- Server version	10.6.22-MariaDB-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `type` enum('comunicado','noticia') NOT NULL,
  `color` varchar(20) DEFAULT '#00897B',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Institucional','institucional','comunicado','#00897B','2026-04-09 11:22:10'),(2,'Recursos Humanos','recursos-humanos','comunicado','#00796B','2026-04-09 11:22:10'),(3,'Tecnologia','tecnologia','comunicado','#004D40','2026-04-09 11:22:10'),(4,'Saúde','saude','noticia','#26A69A','2026-04-09 11:22:10'),(5,'Gestão Hospitalar','gestao-hospitalar','noticia','#00897B','2026-04-09 11:22:10'),(6,'Geral','geral','noticia','#009688','2026-04-09 11:22:10');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `url` varchar(500) NOT NULL,
  `icon` varchar(100) DEFAULT 'link',
  `icon_image` varchar(255) DEFAULT NULL,
  `color` varchar(20) DEFAULT '#00897B',
  `category` enum('sistema','link_rapido','navbar') DEFAULT 'sistema',
  `target` enum('_blank','_self') DEFAULT '_blank',
  `sort_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `is_public` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modules`
--

LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;
INSERT INTO `modules` VALUES (1,'TI','Sistema de Chamados de TI','https://suporte.ti/','support_agent','','#1565c0','sistema','_blank',6,1,1,'2026-04-09 11:22:10','2026-04-13 10:12:43'),(2,'INTERACT','Sistema de Indicadores','https://10.10.1.70:8443/sa/apps/cmn/LauncherLogin.jsp','analytics','','#6a1b9a','sistema','_blank',8,1,1,'2026-04-09 11:22:10','2026-04-13 10:12:43'),(3,'LABORATÓRIO','MED+','https://medmaispara.uniexames.com.br/logins/login','biotech','','#2e7d32','sistema','_blank',2,1,1,'2026-04-09 11:22:10','2026-04-10 13:04:12'),(4,'EGS','Gestão em Saúde','https://sistema-egs.top/uni/login.php','explicit','','#d35400','sistema','_blank',9,1,1,'2026-04-09 11:22:10','2026-04-13 10:12:43'),(5,'PRORADIS','Radiologia Digital','https://10.10.1.20:1311/ris/login','image_search','','#4527a0','sistema','_blank',3,1,1,'2026-04-09 11:22:10','2026-04-10 13:02:16'),(6,'SALUTEM','Prontuário Eletrônico','http://10.10.1.50:9000/hospitalar/#/login','medical_information','','#00838f','sistema','_self',1,1,1,'2026-04-09 11:22:10','2026-04-10 18:34:28'),(7,'GTR','Gestão De Ticket Refeição','http://10.10.11.77:8000/','restaurant','','#e65100','sistema','_blank',11,1,1,'2026-04-09 11:22:10','2026-04-13 10:12:43'),(8,'CHATPRO','Comunicação Interna','https://app.chatpro.com.br/signin','chat','','#1b5e20','sistema','_blank',10,1,1,'2026-04-09 11:22:10','2026-04-13 10:12:43'),(9,'E-mail Institucional','Webmail','https://webmail.institutoacqua.org.br/','email','','#00897b','link_rapido','_blank',1,1,1,'2026-04-09 11:22:10','2026-04-09 14:24:09'),(10,'Portal SESPA','Secretaria de Saúde do Pará','https://www.saude.pa.gov.br','open_in_new',NULL,'#004D40','link_rapido','_blank',2,0,1,'2026-04-09 11:22:10','2026-04-10 14:19:29'),(11,'Solicite acesso aqui','Acesso','https://docs.google.com/forms/d/e/1FAIpQLSeIewEDN6Lt_9vkHbocv6-LIv7SF7tTvy6EAtdDUV60RM6hCg/viewform','account_balance','','#00695c','link_rapido','_blank',3,1,1,'2026-04-09 11:22:10','2026-04-10 14:18:47'),(13,'MANUTENÇÃO','Sistema de chamados de Manutenção','http://10.10.1.15/manutencao/index.php?noAUTO=1','build','','#8a0000','sistema','_self',7,1,1,'2026-04-10 12:21:17','2026-04-13 10:12:43'),(14,'ENG CLÍNICA','Sistema de chamados Engenharia Clínica','https://mgmedical.neovero.com/','monitor_heart','','#0d31bf','sistema','_self',5,1,1,'2026-04-10 12:58:14','2026-04-13 10:12:43'),(15,'APONTATU','Registro de ponto','https://app.apontatu.com.br/credenciamento/login/?1','account_circle','','#ff3300','sistema','_blank',13,1,1,'2026-04-10 15:24:33','2026-04-13 10:12:43'),(16,'ORIS','Emissão de contracheque','https://portal.orisrh.com/','receipt_long','','#f39c12','sistema','_self',14,1,1,'2026-04-10 15:33:02','2026-04-13 10:12:43'),(17,'EPIMED','','https://sso.epimedmonitor.com/Account/Login?ReturnUrl','all_inclusive','','#0267c5','sistema','_blank',4,1,1,'2026-04-13 10:12:33','2026-04-13 10:12:43'),(20,'RAMAIS','Lista de Ramais Internos','public.php?page=ramais','phone_in_talk',NULL,'#00897B','sistema','_self',9,1,1,'2026-04-19 15:56:03','2026-04-19 15:56:03');
/*!40000 ALTER TABLE `modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nav_items`
--

DROP TABLE IF EXISTS `nav_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `nav_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(100) NOT NULL,
  `url` varchar(500) DEFAULT NULL,
  `icon` varchar(80) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `open_new_tab` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `nav_items_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `nav_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nav_items`
--

LOCK TABLES `nav_items` WRITE;
/*!40000 ALTER TABLE `nav_items` DISABLE KEYS */;
INSERT INTO `nav_items` VALUES (1,'Início','index.php','home',NULL,1,1,0),(2,'Comunicados','index.php?page=comunicados','campaign',NULL,2,1,0),(3,'Notícias Externas','index.php?page=noticias','newspaper',NULL,3,1,0),(4,'Sistemas','index.php?page=sistemas','apps',NULL,4,1,0),(7,'Ramais','index.php?page=ramais','phone_in_talk',NULL,5,1,1);
/*!40000 ALTER TABLE `nav_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(300) NOT NULL,
  `summary` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `cover_image_alt` varchar(255) DEFAULT NULL,
  `cover_image_caption` text DEFAULT NULL,
  `type` enum('comunicado','noticia') NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `is_featured` tinyint(1) DEFAULT 0,
  `is_public` tinyint(1) DEFAULT 1,
  `views` int(11) DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  KEY `author_id` (`author_id`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (4,'Palestra de conscientização sobre a violência contra a mulher','palestra-de-conscientizacao-sobre-a-violencia-contra-a-mulher','Nesta terça-feira, 07, a coordenadora Estadual das Mulheres em Situação de Violência Doméstica e Familiar do Tribunal de Justiça do Pará (TJPA), Riane Freitas, apresentou uma palestra sobre o tema Saúde','<span style=\"color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 16px; background-color: rgb(251, 251, 251);\">Nesta terça-feira, 07, a coordenadora Estadual das Mulheres em Situação de Violência Doméstica e Familiar do Tribunal de Justiça do Pará (TJPA), Riane Freitas, apresentou uma palestra sobre o tema Saúde, \"Cuidado e Proteção: Enfrentando a Violência Doméstica contra a Mulher\" aos líderes, colaboradores e profissionais terceirizados, no auditório do Hospital da Mulher do Pará. O objetivo da iniciativa é ampliar informações, o apoio e fortalecimento da rede de proteção às mulheres.</span>','posts/img_69d7fff34fd7a9.79728980.png','','Por: Helen da Silva Alves','comunicado',5,1,'published',0,1,8,'2026-04-09 15:47:13','2026-04-09 15:47:13','2026-04-16 12:05:16'),(5,'Abril pela Segurança do Paciente','abril-pela-seguranca-do-paciente','','<span style=\"color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 16px; background-color: rgb(251, 251, 251);\">Para iniciar o mês de abril, a diretoria do Hospital da Mulher do Pará promoveu uma reunião com as lideranças do hospital com a abordagem do tema alusivo ao Dia Nacional da Segurança do Paciente, celebrado anualmente no dia 01 de abril. Ao longo deste mês, serão realizadas ações com o objetivo de fortalecer e promover as metas internacionais estabelecidas pela Organização Mundial da Saúde (OMS). Neste ano o tema da campanha do Abril pela Segurança do Paciente, do Ministério da Saúde, aborda a \"Qualidade, segurança e vidas protegidas: um compromisso permanente do SUS\".</span>','posts/img_69d803c61f2758.49499237.png','','Por: Helen da Silva Alves','comunicado',1,1,'published',0,1,4,'2026-04-09 16:53:42','2026-04-09 16:53:42','2026-04-18 14:42:07'),(6,'Palestra','palestra','','<span style=\"color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 16px; background-color: rgb(251, 251, 251);\">Na tarde desta quinta-feira, 19, foi realizada a palestra \"Aspectos comportamentais e habilidades psicológicas de liderança\", com uma psicóloga e mentora organizacional Thayana Benmuyal. Na ocasião, participaram os gestores dos setores administrativos e assistenciais do HMPA.</span>','posts/img_69d803fe6b8a79.36750238.png','','Por: Helen da Silva Alves','comunicado',5,1,'published',0,1,16,'2026-04-09 16:54:38','2026-04-09 16:54:38','2026-04-17 12:02:44'),(7,'Campanha de doação de sangue','campanha-de-doacao-de-sangue','','<span style=\"color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 16px; background-color: rgb(251, 251, 251);\">Amanhã, 13 de março, a Fundação Hemopa realizará a 1ª Campanha de doação de sangue, no Hospital da Mulher do Pará, das 8h30 às 16h, no 11º andar. A iniciativa visa reforçar os estoques do banco de sangue. A campanha é voltada para todos os colaboradores, acompanhantes e familiares. Este é um gesto de solidariedade e de amor ao próximo, visto que cada bolsa de sangue pode salvar até 4 vidas. Para doar cadastre-se no link (copie e cole) : https://forms.gle/WXryhmSeTCnsn5Yt7 Orientações importantes: para o dia da doação basta estar bem alimentado, ter entre 16 e 69 anos e trazer documento identificação oficial com foto. Participe dessa corrente do bem!</span>','posts/img_69d8043852cdc4.57028261.png','','Por: Helen da Silva Alves','comunicado',5,1,'published',0,1,2,'2026-04-09 16:55:36','2026-04-09 16:55:36','2026-04-13 14:24:30'),(8,'Semana de aniversário','semana-de-aniversario','','<span style=\"color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 16px; background-color: rgb(251, 251, 251);\">No dia 08 de março, o Hospital da Mulher do Pará completa 1 ano de funcionamento e você, colaborador, está convidado para esta celebração no mês em que se comemora também o Dia Internacional da Mulher! A abertura oficial será no Portal da Amazônia com uma Corrida, às 6h (concentração às 5h30). Na terça-feira, 10, o tradicional corte do bolo de parabéns no ambulatório com as pacientes, às 17h, na terça, quarta e quinta (dias 10 e 12) momento Autocuidado com Mary Kay. Na sexta, dia 13, café da manhã com as colaboradoras e campanha de doação de Sangue, no auditório.</span>','posts/img_69d8045611cdc7.46924401.png','','Por: Helen da Silva Alves','comunicado',5,1,'published',0,1,12,'2026-04-09 16:56:06','2026-04-09 16:56:06','2026-04-18 14:41:56'),(9,'1ª edição do CarnaMetas do HMPA','1-edicao-do-carnametas-do-hmpa','','<span style=\"color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 16px; background-color: rgb(251, 251, 251);\">Com o objetivo de promover o engajamento das equipes multiprofissionais na disseminação das Metas Internacionais de Segurança do Paciente, estimulando a criatividade, integração entre setores e fortalecimento da cultura de segurança no Hospital da Mulher, ainda no clima de Carnaval, foi realizada a I edição do CarnaMetas nesta sexta-feira, dia 06. Com a avaliação criteriosa da comissão de júri, a grande vencedora da competição, no 1º lugar, foi a “Multi Poderosa” da equipe da UTI. O 2º lugar ficou com a equipe “Identifica e Nutre” com profissionais da nutrição clínica e cozinha hospitalar, já o 3º lugar foi para os “Guardiões das Metas”, com equipes das unidades de internação e o 4º lugar para “Acadêmicas do Acolhimento”, com as equipes do SAU, Recepção e Ambulatório. Ao todo, sete equipes estiveram presentes, somando os integrantes da equipe CME, da “Sétima arte”, formada pelas fisioterapeutas e terapeutas ocupacionais e os membros da “Unidos da Identificação” com profissionais da recepção e portaria. Parabéns a todos que participaram da competição que simboliza a unidade de todos os profissionais para promover as metas de segurança do paciente no Hospital da Mulher do Pará</span>','posts/img_69d8047ddfac63.91359270.png','','Por: Helen da Silva Alves','comunicado',2,1,'published',0,1,12,'2026-04-09 16:56:45','2026-04-09 16:56:46','2026-04-18 14:41:42'),(10,'Formação de Brigadistas','formacao-de-brigadistas','','<span style=\"color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 16px; background-color: rgb(251, 251, 251);\">Entre os dias 24 e 27 de fevereiro, o Hospital a Mulher do Pará promoveu o primeiro treinamento para a formação de turmas de brigadistas da unidade. Os instrutores da Brigada de Incêndio ministraram conteúdo teórico e conduziram o momento da prática no estacionamento do hospital. No dia 05 de março, inicia a formação dos colaboradores do noturno.</span>','posts/img_69d804a19fdad0.27705744.png','','Por: Helen da Silva Alves','comunicado',6,1,'published',0,1,23,'2026-04-09 16:57:21','2026-04-09 16:57:22','2026-04-18 14:41:49'),(11,'Hospital da Mulher do Pará já é referência em cirurgias de média e alta complexidade','hospital-da-mulher-do-para-ja-e-referencia-em-cirurgias-de-media-e-alta-complexidade','Unidade registra quase 10 mil procedimentos e atende pacientes dos 144 municípios paraenses, com alta tecnologia como a neuronavegação','<p style=\"margin-bottom: 30px; line-height: 1.7; color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 19.84px;\">O Hospital da Mulher do Pará (HMPA), em Belém, se consolida como uma das principais unidades de referência em assistência cirúrgica feminina na Região Norte. Com atendimento a pacientes dos 144 municípios paraenses, a unidade já soma quase 10 mil cirurgias realizadas, abrangendo procedimentos de baixa, média e alta complexidade em especialidades como ginecologia, mastologia, cirurgia geral, neurocirurgia, urologia e plástica reparadora.</p><p style=\"margin-bottom: 30px; line-height: 1.7; color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 19.84px;\">Entre os diferenciais da unidade está a incorporação de tecnologias avançadas que ampliam a segurança e a precisão dos procedimentos. Um dos destaques é o uso do neuronavegador, um sistema que funciona como um “GPS” cirúrgico, integrando exames de imagem, como tomografia e ressonância magnética, ao planejamento e à execução da cirurgia em tempo real.</p><p style=\"margin-bottom: 30px; line-height: 1.7; color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 19.84px;\">A tecnologia foi recentemente aplicada no tratamento da paciente, Darlene Costa, 35 anos, residente no município de Bragança, nordeste paraense. Ela foi submetida a uma Derivação Ventrículo-Peritoneal (DVP), procedimento indicado para reduzir a pressão intracraniana por meio da implantação de uma válvula e de um cateter que drenam o excesso de líquido cerebral.</p><p style=\"margin-bottom: 30px; line-height: 1.7; color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 19.84px;\">A neurocirurgiã, Maíra Piani, integrante da equipe responsável pelo procedimento, explica que o uso do neuronavegador foi essencial para garantir maior precisão durante a cirurgia, que teve duração média de uma hora e meia.</p>','posts/img_69d80adf17ffa0.23160445.png','','Texto: Ascom HMPA','noticia',4,1,'published',1,1,33,'2026-04-09 17:23:59','2026-04-09 17:23:59','2026-04-19 10:08:43'),(12,'Hospital da Mulher do Pará alcança 500 mil atendimentos e transforma vida de pacientes em um ano','hospital-da-mulher-do-para-alcanca-500-mil-atendimentos-e-transforma-vida-de-pacientes-em-um-ano','Unidade estadual, referência em casos de endometriose e ginecologia especializada, já realizou 10 mil cirurgias, atendendo mulheres de todos os municípios paraenses via encaminhamento, inclusive, pelo programa \"Por Todas Elas\"','<p style=\"margin-bottom: 30px; line-height: 1.7; color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 19.84px;\">A dor persistente que afetava a rotina da autônoma Adriana Teixeira, 46 anos, moradora do município de Ananindeua, finalmente teve explicação e tratamento adequado no Hospital da Mulher do Pará (HMPA). Após sucessivas tentativas sem diagnóstico em outros serviços, foi na unidade estadual que ela descobriu a endometriose e conseguiu realizar a cirurgia necessária.</p><p style=\"margin-bottom: 30px; line-height: 1.7; color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 19.84px;\">“Eu fiquei muito alegre por ter feito a minha cirurgia, porque eu sentia dores todos os dias, mas não dava nada nos exames que eu fazia. Eu sabia que não era normal a dor no ventre e nas pernas. Foi nesse hospital que descobriram a endometriose. Fiz todos os exames e a médica explicou que era necessária a cirurgia”, relatou a paciente.</p><p style=\"margin-bottom: 30px; line-height: 1.7; color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 19.84px;\">O caso ilustra um marco expressivo alcançado pelo HMPA: 500 mil atendimentos realizados em pouco mais de um ano de funcionamento, contemplando mulheres de todos os 144 municípios paraenses. Nesse período, a unidade contabilizou mais de 390 mil exames de imagem e laboratoriais; ultrapassou 100 mil consultas médicas; realizou quase dez mil cirurgias e efetuou cerca de quatro mil atendimentos de urgência e emergência.</p><p style=\"margin-bottom: 30px; line-height: 1.7; color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 19.84px;\">Adriana foi encaminhada ao hospital por meio do programa “Por Todas Elas”, iniciativa que leva serviços gratuitos de saúde, cidadania e assistência social às Usinas da Paz (UsiPaz), ampliando o acesso da população a atendimentos especializados.</p><p style=\"margin-bottom: 30px; line-height: 1.7; color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 19.84px;\"><span style=\"font-weight: bolder;\">Cuidado especializado -</span>&nbsp;Referência estadual em saúde feminina, o Hospital da Mulher do Pará oferece linhas de cuidado específicas para condições como endometriose, gigantomastia, caracterizada pelo crescimento excessivo das mamas, e tratamento de outras doenças crônicas, como a fibromialgia, além de realizar procedimentos cirúrgicos de baixa, média e alta complexidade.</p><p style=\"margin-bottom: 30px; line-height: 1.7; color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 19.84px;\">Outro diferencial da unidade é a atuação integrada à rede de proteção estadual para o enfrentamento da violência contra a mulher. O hospital dispõe de atendimento médico e psicossocial especializado, além da “Sala Lilás”, espaço seguro que permite o registro de denúncias por meio de um totem de autoatendimento da Delegacia Especializada no Atendimento à Mulher (Deam), vinculada à Secretaria de Estado de Segurança Pública e Defesa Social (Segup).</p><p style=\"margin-bottom: 30px; line-height: 1.7; color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 19.84px;\">A diretora-geral do HMPA, médica Nelma Machado, destaca que os resultados refletem o compromisso com a ampliação do acesso e a humanização do atendimento. “Alcançar a marca de 500 mil atendimentos em pouco mais de um ano representa não apenas um número expressivo, mas a transformação concreta na vida de milhares de mulheres paraenses. Nosso compromisso é garantir um atendimento integral, humanizado e resolutivo, desde o diagnóstico até o tratamento, assegurando dignidade e qualidade de vida às pacientes”, afirmou a gestora.</p><p style=\"margin-bottom: 30px; line-height: 1.7; color: rgb(33, 37, 41); font-family: system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, &quot;Noto Sans&quot;, &quot;Liberation Sans&quot;, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 19.84px;\">O Hospital da Mulher do Pará é uma unidade vinculada à Secretaria de Estado de Saúde Pública (Sespa) e é composta por onze pavimentos, com 120 leitos, sendo 100 de internações clínicas e cirúrgicas e 20 leitos de UTI adulto, ambulatórios com mais de 20 especialidades, serviços de exames e de urgência e emergência especializada.</p><div><br></div>','posts/img_69d95d2cc02d62.08136478.webp','https://www.agenciapara.com.br/noticia/76247/hospital-da-mulher-do-para-alcanca-500-mil-atendimentos-e-transforma-vida-de-pacientes-em-um-ano','','noticia',1,3,'published',1,1,47,'2026-04-10 17:25:24','2026-04-10 17:25:04','2026-04-19 12:49:29');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ramais`
--

DROP TABLE IF EXISTS `ramais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ramais` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `andar` varchar(50) NOT NULL,
  `setor` varchar(150) NOT NULL,
  `ramal` varchar(20) NOT NULL,
  `linha` varchar(30) DEFAULT '',
  `sort_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ramais`
--

LOCK TABLES `ramais` WRITE;
/*!40000 ALTER TABLE `ramais` DISABLE KEYS */;
INSERT INTO `ramais` VALUES (1,'Térreo','Recepção SADT','232','(91) 3197-6328',1,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(2,'Térreo','COORD SADT','255','',2,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(3,'Térreo','Portaria 14','203','',3,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(4,'Térreo','Recepção Visitante','220','',4,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(5,'Térreo','Recepção Urgência','200','',5,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(6,'Térreo','Recepção Internação Eletiva','226','',6,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(7,'Térreo','PA','201','',7,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(8,'Térreo','Ressonância e Tomografia','242','',8,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(9,'Térreo','Ultrassonografia','243','',9,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(10,'Térreo','Ecocardiograma','205','',10,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(11,'Térreo','Mapa/Holter','249','',11,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(12,'Térreo','Assistência Social','237','(91) 3197-6333',12,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(13,'1° Andar','Recepção Check-in/Check-out','236','',13,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(14,'1° Andar','Recepção 01','246','',14,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(15,'1° Andar','Recepção 02','248','',15,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(16,'1° Andar','Recepção 03','241','',16,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(17,'1° Andar','Recepção 04','204','',17,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(18,'1° Andar','Recepção 05','211','',18,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(19,'1° Andar','Entrega de Exames (Laboratório)','259','',19,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(20,'1° Andar','NVE','258','',20,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(21,'1° Andar','NIR','234','(91) 3197-6322',21,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(22,'1° Andar','SCIH','239','(91) 3197-6329',22,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(23,'1° Andar','SAU','299','(91) 3197-6343',23,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(24,'1° Andar','COORD Ambulatório','238','',24,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(25,'1° Andar','Supervisão de Atendimento','265','',25,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(26,'2° Andar','TecCity','227','',26,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(27,'2° Andar','Nutrição Produção','250','',27,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(28,'2° Andar','Copa','256','',28,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(29,'2° Andar','Nutrição Clínica','247','',29,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(30,'2° Andar','COORD Nutrição Clínica','264','',30,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(31,'2° Andar','Faturamento','240','',31,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(32,'3° Andar','CAF','207','(91) 3197-6342',32,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(33,'3° Andar','Almoxarifado','254','',33,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(34,'3° Andar','CME','222','',34,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(35,'3° Andar','HEMOPA','218','',35,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(36,'3° Andar','Lavanderia','233','',36,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(37,'3° Andar','Laboratório','253','',37,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(38,'3° Andar','Gerência de Enfermagem','216','',38,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(39,'3° Andar','Recepção Direção','213','',39,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(40,'3° Andar','Direção Administrativa','244','',40,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(41,'3° Andar','Direção Geral','221','',41,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(42,'3° Andar','Direção Assistencial','223','',42,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(43,'3° Andar','COORD Administrativa','262','',43,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(44,'3° Andar','Imperador','229','',44,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(45,'3° Andar','Recursos Humanos','209','(91) 3197-6338',45,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(46,'3° Andar','NEP','235','(91) 3197-6336',46,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(47,'3° Andar','SESMET','257','',47,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(48,'3° Andar','Compras','252','(91) 3197-6344',48,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(49,'4° Andar','COORD Bloco Cirúrgico','260','',49,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(50,'4° Andar','Bloco Cirúrgico','215','',50,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(51,'4° Andar','Engenharia Clínica','219','',51,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(52,'4° Andar','Patrimônio','224','',52,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(53,'4° Andar','TI','*5130','',53,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(54,'5° Andar','UTI','208','',54,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(55,'5° Andar','COORD Fisioterapia e UTI','263','',55,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(56,'6° Andar','Posto Internação 6','212','',56,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(57,'7° Andar','Posto Internação 7','210','',57,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(58,'8° Andar','Posto Internação 8','206','',58,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(59,'8° Andar','Farmácia Central','217','',59,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(60,'9° Andar','Posto Internação 9','214','',60,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(61,'9° Andar','SAME','251','',61,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(62,'10° Andar','Posto Internação 10','230','',62,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(63,'11° Andar','MAPI','228','',63,1,'2026-04-19 16:11:01','2026-04-19 16:11:01'),(64,'11° Andar','Auditório','280','',64,1,'2026-04-19 16:11:01','2026-04-19 16:11:01');
/*!40000 ALTER TABLE `ramais` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','textarea','image','boolean','color','json') DEFAULT 'text',
  `label` varchar(150) DEFAULT NULL,
  `group_name` varchar(80) DEFAULT 'general',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'site_name','Intranet Acqua','text','Nome do Site','general','2026-04-09 11:49:26'),(2,'site_tagline','Hospital da Mulher Do Pará','text','Subtítulo','general','2026-04-10 12:42:31'),(3,'site_logo','img_69d807771fdef7.58733043.png','image','Logo','general','2026-04-09 17:09:27'),(4,'footer_text','© 2026 Hospital da Mulher do Pará. Todos os direitos reservados.','textarea','Rodapé','general','2026-04-09 11:23:11'),(5,'primary_color','#19c8b6','color','Cor Primária','appearance','2026-04-09 11:46:15'),(6,'secondary_color','#02a78b','color','Cor Secundária','appearance','2026-04-09 11:46:15'),(7,'posts_per_page','10','text','Posts por Página','general','2026-04-09 11:22:10'),(8,'allow_registration','0','boolean','Permitir Auto-cadastro','auth','2026-04-09 11:22:10'),(9,'session_timeout','480','text','Timeout da Sessão (min)','auth','2026-04-09 11:22:10'),(10,'hero_title','Hospital da Mulher Do Pará','text','Título do Hero','appearance','2026-04-09 14:09:53'),(11,'hero_subtitle','Portal de Comunicação Institucional','text','Subtítulo do Hero','appearance','2026-04-09 13:56:45'),(12,'image_max_width','1920','text','Largura máx. imagem (px)','media','2026-04-09 11:22:10'),(13,'image_quality','85','text','Qualidade JPEG (1-100)','media','2026-04-09 11:22:10');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','editor','user') DEFAULT 'user',
  `sector` varchar(100) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `dark_mode` tinyint(1) DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrador','admin@admin.com','$2y$10$.QKSQUglmAzZKKEXDhLXfeIMPU9B7lgt16YZPHOFINFvBO1vg54aC','admin','TI',NULL,1,0,'2026-04-19 16:18:02','2026-04-09 11:22:10','2026-04-19 16:23:22'),(2,'Equipe Comunicação','comunicacao@admin.com','$2y$10$.QKSQUglmAzZKKEXDhLXfeIMPU9B7lgt16YZPHOFINFvBO1vg54aC','editor','Comunicação',NULL,1,0,NULL,'2026-04-09 11:22:10','2026-04-19 15:20:38'),(3,'Helen Silva','helen.silva@institutoacqua.org.br','$2y$12$byC37kf0Lxa8WJS/i7Ebe.55rorX26FAqhtl9zZSkYfzgrEgD5PoW','editor','comunicação',NULL,1,0,'2026-04-10 17:18:40','2026-04-10 17:18:11','2026-04-10 17:19:50');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-19 16:40:43
