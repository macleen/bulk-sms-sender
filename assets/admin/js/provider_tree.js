class ProviderTree {
    static __instance = null;
    static $treeContainer = null;  // Static property to hold the FancyTree instance

    constructor(config = null) {
        if (ProviderTree.__instance) return ProviderTree.__instance;
        this.config = config;
        ProviderTree.__instance = this;
    }

    static handle_tree_click_event(e, data) {
        const node = data.node;
        console.log('Clicked node:', node);

        if (node.data.name && node.data.location) {
            jQuery("#macleen-selected-purchasable-plugin-show").text(node.data.name);
            jQuery("#macleen-selected-purchasable-plugin-purchase")
                .attr('data-selected-plugin', node.data.name)
                .attr('data-plugin-location', node.data.location);
        } else {
            jQuery("#macleen-selected-purchasable-plugin-show").text('None');
            jQuery("#macleen-selected-purchasable-plugin-purchase")
                .attr('data-selected-plugin', '')
                .attr('data-plugin-location', '');
        }
    }

    show_plugin_tree(dom_container, reload = false) {

        const $treeContainer = jQuery(dom_container);
        ProviderTree.$treeContainer = $treeContainer; // Store the instance in the static property

        // If reload is true or FancyTree instance exists, proceed to destroy and rebuild the tree
        if (reload || ProviderTree.$treeContainer.data("fancytree")) {
            // Destroy the existing FancyTree instance
            ProviderTree.$treeContainer.fancytree("destroy");

            // Force a delay to ensure the DOM is updated
            setTimeout(() => {
                // Fetch new data and rebuild the tree
                this.config.server.get_plugins_tree().then(response => {
                    console.log('plugin_tree response ===> ', response);

                    const total = response.data['total'];
                    const source = response.data['source'];

                    jQuery('#macleen-installed-plugins').text(total.installed);
                    jQuery('#macleen-purchasable-plugins').text(total.purchasable);

                    // Rebuild FancyTree
                    console.log('Initializing FancyTree with source:', source);
                    ProviderTree.$treeContainer.fancytree({
                        source: source,
                        click: ProviderTree.handle_tree_click_event,
                        extensions: ["glyph"],
                        glyph: {
                            preset: "awesome4",
                        },
                    });
                }).catch(error => {
                    console.error("Error fetching plugin tree:", error);
                });
            }, 50); // Delay to allow DOM update
        } else {
            this.config.server.get_plugins_tree().then(response => {
                const total = response.data['total'];
                const source = response.data['source'];
                jQuery('#macleen-installed-plugins').text(total.installed);
                jQuery('#macleen-purchasable-plugins').text(total.purchasable);

                // Initialize FancyTree for the first time
                ProviderTree.$treeContainer.fancytree({
                    source: source,
                    click: ProviderTree.handle_tree_click_event,
                    extensions: ["glyph"],
                    glyph: {
                        preset: "awesome4",
                    },
                });
            }).catch(error => {
                console.error("Error fetching plugin tree:", error);
            });
        }
    }
}
