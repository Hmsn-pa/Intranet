-- ============================================================
-- MIGRAÇÃO: Campos hero_title e hero_subtitle
-- Execute no phpMyAdmin → banco intranet_acqua → aba SQL
-- ============================================================
USE intranet_acqua;

INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `label`, `group_name`) VALUES
('hero_title',    getSetting('site_tagline','Unidade de Saúde'),                             'text', 'Título do Hero',    'appearance'),
('hero_subtitle', 'Pronto socorro — Portal de Comunicação Institucional',   'text', 'Subtítulo do Hero', 'appearance');

-- Atualiza valores mesmo se já existirem:
UPDATE `settings` SET `setting_value` = getSetting('site_tagline','Unidade de Saúde')                             WHERE `setting_key` = 'hero_title';
UPDATE `settings` SET `setting_value` = 'Pronto socorro — Portal de Comunicação Institucional'   WHERE `setting_key` = 'hero_subtitle';
