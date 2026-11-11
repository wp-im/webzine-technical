<?php
namespace Webzine\Technical;
if ( ! defined( 'ABSPATH' ) ) { exit; }
function widgets() {
	$areas = array(
		'drawer-top' => __( '抽屉 · 上方', 'webzine-technical' ), 'drawer-bottom' => __( '抽屉 · 下方', 'webzine-technical' ),
		'home-top' => __( '首页 · 顶部', 'webzine-technical' ), 'home-middle' => __( '首页 · 内容模块（可排序）', 'webzine-technical' ), 'home-bottom' => __( '首页 · 底部', 'webzine-technical' ),
		'archive-top' => __( '归档 · 上方', 'webzine-technical' ), 'archive-bottom' => __( '归档 · 下方', 'webzine-technical' ),
		'article-aside' => __( '文章 · 资料区（窄屏移至正文后）', 'webzine-technical' ), 'article-bottom' => __( '文章 · 正文后', 'webzine-technical' ),
		'page-bottom' => __( '页面 · 正文后', 'webzine-technical' ), 'footer-main' => __( '页脚 · 主区', 'webzine-technical' ), 'footer-small' => __( '页脚 · 补充信息', 'webzine-technical' ),
	);
	foreach ( $areas as $id => $name ) {
		register_sidebar( array( 'id' => 'wz-' . $id, 'name' => $name, 'description' => __( '使用原生小工具或区块添加内容；空区域不会占位。', 'webzine-technical' ), 'before_widget' => '<section id="%1$s" class="wz-widget %2$s">', 'after_widget' => '</section>', 'before_title' => '<h2 class="wz-widget-title">', 'after_title' => '</h2>' ) );
	}
	register_widget( __NAMESPACE__ . '\\Feed_Widget' );
	register_widget( __NAMESPACE__ . '\\Topics_Widget' );
}
add_action( 'widgets_init', __NAMESPACE__ . '\\widgets' );
class Feed_Widget extends \WP_Widget {
	public function __construct() {
		parent::__construct( 'wz_feed', __( 'Webzine · 编辑文章组', 'webzine-technical' ), array( 'description' => __( '选择分类或人工指定文章，按列表、网格、索引展示。', 'webzine-technical' ), 'customize_selective_refresh' => true ) );
	}
	public function widget( $args, $instance ) {
		$query = array( 'posts_per_page' => max( 1, min( 20, absint( $instance['count'] ?? 5 ) ) ) );
		if ( ! empty( $instance['category'] ) ) { $query['cat'] = absint( $instance['category'] ); }
		$ids = array_filter( array_map( 'absint', explode( ',', $instance['ids'] ?? '' ) ) );
		if ( $ids ) { $query['post__in'] = $ids; $query['orderby'] = 'post__in'; }
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered markup.
		if ( ! empty( $instance['title'] ) ) { echo $args['before_title'] . esc_html( apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) ) . $args['after_title']; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$view = in_array( $instance['view'] ?? '', array( 'list', 'grid', 'index' ), true ) ? $instance['view'] : 'list';
		feed( $query, 'wz-feed-' . $view );
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	public function form( $instance ) {
		foreach ( array( 'title' => __( '模块标题', 'webzine-technical' ), 'count' => __( '文章数量（1–20）', 'webzine-technical' ), 'ids' => __( '人工精选 ID（逗号分隔；留空按分类）', 'webzine-technical' ) ) as $key => $label ) {
			printf( '<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="%4$s" value="%5$s"></p>', esc_attr( $this->get_field_id( $key ) ), esc_html( $label ), esc_attr( $this->get_field_name( $key ) ), $key === 'count' ? 'number' : 'text', esc_attr( $instance[ $key ] ?? ( $key === 'count' ? 5 : '' ) ) );
		}
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'category' ) ) . '">' . esc_html__( '文章分类', 'webzine-technical' ) . '</label>';
		wp_dropdown_categories( array( 'name' => $this->get_field_name( 'category' ), 'id' => $this->get_field_id( 'category' ), 'selected' => absint( $instance['category'] ?? 0 ), 'show_option_all' => __( '所有分类', 'webzine-technical' ), 'hide_empty' => false ) ); echo '</p>';
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'view' ) ) . '">' . esc_html__( '展示方式', 'webzine-technical' ) . '</label><select id="' . esc_attr( $this->get_field_id( 'view' ) ) . '" name="' . esc_attr( $this->get_field_name( 'view' ) ) . '">';
		foreach ( array( 'list' => __( '图文列表', 'webzine-technical' ), 'grid' => __( '图文网格', 'webzine-technical' ), 'index' => __( '文字索引', 'webzine-technical' ) ) as $key => $label ) { echo '<option value="' . esc_attr( $key ) . '" ' . selected( $instance['view'] ?? 'list', $key, false ) . '>' . esc_html( $label ) . '</option>'; } echo '</select></p>';
	}
	public function update( $new, $old ) {
		return array( 'title' => sanitize_text_field( $new['title'] ?? '' ), 'count' => max( 1, min( 20, absint( $new['count'] ?? 5 ) ) ), 'category' => absint( $new['category'] ?? 0 ), 'ids' => implode( ',', array_filter( array_map( 'absint', explode( ',', $new['ids'] ?? '' ) ) ) ), 'view' => in_array( $new['view'] ?? '', array( 'list', 'grid', 'index' ), true ) ? $new['view'] : 'list' );
	}
}
class Topics_Widget extends \WP_Widget {
	public function __construct() { parent::__construct( 'wz_topics', __( 'Webzine · 专题目录', 'webzine-technical' ), array( 'customize_selective_refresh' => true ) ); }
	public function widget( $args, $instance ) {
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( ! empty( $instance['title'] ) ) { echo $args['before_title'] . esc_html( $instance['title'] ) . $args['after_title']; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		topics( 12 ); echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	public function form( $instance ) { printf( '<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" value="%4$s"></p>', esc_attr( $this->get_field_id( 'title' ) ), esc_html__( '目录标题', 'webzine-technical' ), esc_attr( $this->get_field_name( 'title' ) ), esc_attr( $instance['title'] ?? '' ) ); }
	public function update( $new, $old ) { return array( 'title' => sanitize_text_field( $new['title'] ?? '' ) ); }
}
