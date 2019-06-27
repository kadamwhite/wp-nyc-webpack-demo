import { Fragment } from '@wordpress/element';

export const name = 'wpnyc/demo-1';

export const settings = {
	title: 'Demo Block',
	description: 'A simple demo block',
	category: 'widgets',
	icon: 'star-filled',
	edit( { isSelected } ) {
		return (
			<Fragment>
				<p>Demo Editor Content</p>
				{ isSelected && (
					<p><em>(selected, woo!)</em></p>
				) }
			</Fragment>
		);
	},
	save() {
		return (
			<p>Demo Saved Content</p>
		);
	},
};
