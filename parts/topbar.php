<?php namespace Webzine\Technical; ?>
<header class="wz-topbar">
 <div class="wz-top-brand"><?php if ( has_custom_logo() ) { the_custom_logo(); } ?><?php if(display_header_text()): ?><a class="wz-site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a><?php endif; ?></div>
 <nav class="wz-primary" aria-label="<?php esc_attr_e( '顶部导航', 'webzine-technical' ); ?>"><?php menu( 'primary' ); ?></nav>
 <div class="wz-top-tools"><button type="button" class="wz-icon-button" data-open="wz-search" aria-label="<?php esc_attr_e( '搜索', 'webzine-technical' ); ?>"><?php echo icon( 'search' ); ?></button><details class="wz-utility"><summary aria-label="<?php esc_attr_e( '更多操作', 'webzine-technical' ); ?>">•••</summary><div><nav aria-label="<?php esc_attr_e( '辅助链接', 'webzine-technical' ); ?>"><?php menu( 'utility' ); ?></nav><button type="button" data-share><?php esc_html_e( '分享此页', 'webzine-technical' ); ?></button><button type="button" data-print><?php esc_html_e( '打印', 'webzine-technical' ); ?></button></div></details></div>
</header>
