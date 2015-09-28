<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Le script de création wp-config.php utilise ce fichier lors de l'installation.
 * Vous n'avez pas à utiliser l'interface web, vous pouvez directement
 * renommer ce fichier en "wp-config.php" et remplir les variables à la main.
 * 
 * Ce fichier contient les configurations suivantes :
 * 
 * * réglages MySQL ;
 * * clefs secrètes ;
 * * préfixe de tables de la base de données ;
 * * ABSPATH.
 * 
 * @link https://codex.wordpress.org/Editing_wp-config.php 
 * 
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'jourblanex2015');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'jourblanex2015');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'BlackDay2015');

/** Adresse de l'hébergement MySQL. */
define('DB_HOST', 'jourblanex2015.mysql.db');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8');

/** Type de collation de la base de données. 
  * N'y touchez que si vous savez ce que vous faites. 
  */
define('DB_COLLATE', '');

/**#@+
 * Clefs uniques d'authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant 
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n'importe quel moment, afin d'invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         ')EC7&8`Uw[;w2E m$^=x<9ml]4u-=~-Tkxk`E8FbM6X&=bWxO#+p+`aBbiyS.b?r');
define('SECURE_AUTH_KEY',  'tuJp&GFc>YW?:05k^6DY6@<xmj45~p3D`f+*rk3Fn.y+c-:nXc-IVf1!;}NEl$1]');
define('LOGGED_IN_KEY',    '?~r3WLkA$|rn6~&{b0|dt+3^Flf5u4&Nk(fKG7[(%=z]YFCx|Ana-y>o*~2<N!T|');
define('NONCE_KEY',        '*Y`F.-QKE@O]U&v#,[X)=J_-L&amKbK#}P^yXobW`fmjvM8E#q|QoeL&A#4U*Q)+');
define('AUTH_SALT',        'EmB;g p@--M-Y1^N},3u[|dc(.80#^NY5gL@@L/1)+l(;j!iV),kaj11-rBq>Wst');
define('SECURE_AUTH_SALT', '|LB}Mp{?E$59^&1`8JA9iiplU4e@`o?ZL%jV D3WJ.gPx-i@ZNs5j5-hFDl+&bdc');
define('LOGGED_IN_SALT',   '&Zv=2|ELF1yLL*ya 2dm{w#&}.z-Fly+T[QA)Mi$|,]NR^3gbb<h6S~$Gn!OFhJL');
define('NONCE_SALT',       '>@%EAMUsG^7.cVTV^Lloa>r}/cHqb|p$YS>l_#/f3AE<cb]+xzh|z>HgCX/B_;Sc');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique. 
 * N'utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés!
 */
$table_prefix  = 'blog_';

/** 
 * Pour les développeurs : le mode déboguage de WordPress.
 * 
 * En passant la valeur suivante à "true", vous activez l'affichage des
 * notifications d'erreurs pendant votre essais.
 * Il est fortemment recommandé que les développeurs d'extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de 
 * développement.
 * 
 * Pour obtenir plus d'information sur les constantes 
 * qui peuvent être utilisée pour le déboguage, consultez le Codex.
 * 
 * @link https://codex.wordpress.org/Debugging_in_WordPress 
 */ 
define('WP_DEBUG', false); 

/* C'est tout, ne touchez pas à ce qui suit ! Bon blogging ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');