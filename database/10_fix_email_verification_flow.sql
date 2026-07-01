-- Corrige usuarios criados pelo fluxo antigo de verificacao de email.
-- O fluxo antigo salvava user.email como string vazia e deixava o email real
-- apenas em email_verification.email_temporario.

UPDATE user u
INNER JOIN email_verification ev ON ev.user_id = u.id
SET u.email = ev.email_temporario
WHERE (u.email = '' OR u.email IS NULL)
  AND ev.email_temporario IS NOT NULL
  AND ev.email_temporario <> '';
