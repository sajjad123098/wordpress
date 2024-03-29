import React from 'react';

const DebugRender = ( { forLabel = '' } ) => {
	const renders = React.useRef( 0 );

	return (
		<span>
			<b>Renders for { forLabel }:</b> { renders.current++ }
		</span>
	);
};

export default DebugRender;
