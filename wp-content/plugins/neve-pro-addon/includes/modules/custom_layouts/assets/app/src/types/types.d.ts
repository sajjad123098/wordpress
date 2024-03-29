export type PostMeta = {
	'custom-layout-options-layout': string;
	'custom-layout-options-hook': string;
	'custom-layout-options-sidebar': string;
	'custom-layout-options-sidebar-action': string;
	'custom-layout-options-should-expire': boolean;
	'custom-layout-expiration-date': string;
	'custom-layout-options-inside-display': string;
	'custom-layout-options-events-no': number;
	'custom-layout-options-priority-v2': number;
	'custom-layout-conditional-logic': string;
};

/**
 * Type for Options
 */
export type Option = {
	id?: number;
	value: string | number | readonly string[] | undefined;
	label?: string;
	disabled?: boolean;
};

type SidebarOptionsType = {
	conditions: {
		root: Record<
			string,
			{ label: string; choices: Record< string, string >[] }
		>;
		map: Record< string, string[] >;
		end: Record< string, Record< string, string > >;
	};
	layouts: Record< string, string >;
	hooks: Option[];
	sidebarPositions: Record< string, string >;
	sidebarActions: Record< string, string >;
	insidePositions: Option[];
};

interface LocalizedData {
	ajaxOptions: string;
	customEditorEndpoint: string;
	nonce: string;
	phpError: string;
	magicTags: Record< string, Record< string, [  ] > >;
	strings: Record< string, string >;
	conditionMap: Record< string, Record< string, string > >;
	sidebarOptions: SidebarOptionsType;
	renderDebug: string;
	customLayoutOptions: {
		templates: Record< string, string >;
		components: Record< string, string >;
		hooks: Option[];
	};
	newLayoutUrl: string;
}

declare global {
	interface Window {
		neveCustomLayouts: LocalizedData;
		wp: {
			data: {
				dispatch: (
					value: string
				) => {
					createNotice: (
						type: string,
						text: string,
						args: Record< string, unknown >
					) => never;
				};
			};
		};
	}
}
