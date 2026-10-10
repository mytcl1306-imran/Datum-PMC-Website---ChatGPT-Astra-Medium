<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> · <?= e($brand) ?></title><meta name="description" content="<?= e($description) ?>">
<meta name="csrf-token" content="<?= e($_SESSION['csrf']) ?>"><meta name="color-scheme" content="light dark">
<?php if($noindex): ?><meta name="robots" content="noindex,nofollow"><?php endif ?>
<link rel="canonical" href="<?= e($canonical) ?>"><meta property="og:title" content="<?= e($title) ?>"><meta property="og:description" content="<?= e($description) ?>"><meta property="og:type" content="website"><meta property="og:url" content="<?= e($canonical) ?>"><meta name="twitter:card" content="summary">
<link rel="icon" type="image/svg+xml" href="/assets/mark.svg"><link rel="stylesheet" href="/assets/site.css"><script src="/assets/site.js" defer></script>
<?php if($template==='workspace'): ?><script src="/assets/workspace.js" defer></script><?php endif ?>
</head>
<body>
<a class="skip" href="#main">Skip to content</a>
<header class="header wrap"><a class="brand" href="/" aria-label="<?= e($brand) ?> home"><img src="/assets/mark.svg" width="34" height="34" alt=""><span><?= e($brand) ?><small><?= e($settings['brand_descriptor']) ?></small></span></a>
<button class="menu-toggle" aria-controls="navigation" aria-expanded="false">Menu</button>
<nav id="navigation" aria-label="Main navigation"><a href="/about">Approach</a><a href="/services">Expertise</a><a href="/projects">Projects</a><a href="/resources">Resources</a><a href="/portal">Client portal ↗</a></nav>
<div class="header-actions"><button id="theme-toggle" class="icon-button" aria-label="Change colour theme">◐</button><a class="button small" href="/contact">Let’s talk <span>↗</span></a></div></header>
<main id="main"><?php require __DIR__.'/'.$template.'.php'; ?></main>
<footer class="footer"><div class="wrap"><div class="footer-top"><div><a class="brand" href="/"><?= e($brand) ?><span class="dot">.</span></a><p><?= e($settings['footer_line']) ?></p></div><div><h2>Explore</h2><a href="/services">Expertise</a><a href="/projects">Projects</a><a href="/about">Approach</a></div><div><h2>Connect</h2><a href="/contact">Start a conversation</a><a href="/portal">Client workspace</a><a href="/faq">Frequently asked questions</a></div><div><h2>Stay informed</h2><form id="newsletter"><label for="newsletter-email">Register interest in future insights</label><div class="inline"><input id="newsletter-email" name="email" type="email" required autocomplete="email"><button type="submit" aria-label="Register interest">↗</button></div><label class="check"><input name="consent" type="checkbox" required> I agree to be contacted about this newsletter.</label><p class="form-message" role="status"></p></form></div></div><div class="footer-bottom"><span>© <?= date('Y') ?> <?= e($brand) ?></span><div><a href="/cookies">Cookies</a><a href="/sitemap">Sitemap</a><a href="/search">Search</a></div><span>Considered from the ground up.</span></div></div></footer>
</body></html>
