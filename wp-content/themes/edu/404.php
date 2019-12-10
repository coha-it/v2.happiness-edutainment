<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Edutainment_2016
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<section class="error-404 not-found">
				<header class="page-header">
					<h1 class="page-title"><?php esc_html_e( 'Oops! Da ist was schief gelaufen.', 'edu' ); ?></h1>
				</header><!-- .page-header -->

				<div class="page-content">
					<p><?php esc_html_e( 'Lieber Ausbildungsprogrammteilnehmer! Da Sie nur das Online-Paket gebucht haben, ist ein Zugriff auf die Foren und eine Interaktion mit den anderen Teilnehmern leider nicht möglich. Gerne beraten wir Sie bzgl. des Gesamt-Pakets, falls Sie eine Änderung Ihrer Buchung wünschen', 'edu' ); ?></p>
				<p>Bei weiteren Fragen wenden Sie sich gerne auch an unser Support Team: <a href="http://coha.happiness-edutainment.de/hilfe-faq-kontakt">Hilfe Seite</a></p>
					

				</div><!-- .page-content -->

				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
