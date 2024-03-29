var Track = function ( panel, model, view ) {
  const widgetsToTrack = [
    'neve_flipcard',
    'neve_typed_headline',
    'neve_team_member',
    'neve_share_buttons',
    'neve_review_box',
    'neve_progress_circle',
    'neve_instagram_feed',
    'neve_custom_field',
    'neve_content_switcher',
    'neve_banner',
  ];

  const currentWidget = model.attributes.widgetType;
  if ( widgetsToTrack.indexOf( currentWidget ) === -1 ) {
    return;
  }

  window.tiTrk?.with( 'neve' ).set( 'elementor-widgets-' + currentWidget, {
    feature: 'elementor-widgets',
    featureComponent: currentWidget,
    featureValue: 'enabled',
  } );
};

jQuery( window ).on( 'elementor/frontend/init', function () {
  if ( typeof elementor === 'undefined' || typeof elementor.hooks === 'undefined' ) {
    return;
  }
  elementor.hooks.addAction( 'panel/open_editor/widget', Track );
} );
