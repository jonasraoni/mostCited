/* eslint-disable */
describe('Most Cited Submissions plugin tests', function () {

	it('Disable Most Cited Submissions', function () {
		cy.login('admin', 'admin', 'publicknowledge');
		cy.get('nav[class="app__nav"] a:contains("Website")').click();
		cy.get('button[id="plugins-button"]').click();
		// disable plugin if enabled
		cy.get('input[id^="select-cell-mostcitedplugin-enabled"]')
			.then($btn => {
				if ($btn.attr('checked') === 'checked') {
					cy.get('input[id^="select-cell-mostcitedplugin-enabled"]').click();
					cy.get('div[class*="pkp_modal_panel"] button[class*="pkpModalConfirmButton"]').click();
					cy.get('div:contains(\'The plugin "Most Cited Submissions" has been disabled.\')');
				}
			});
	});

	it('Enable Most Cited Submissions', function () {
		cy.login('admin', 'admin', 'publicknowledge');
		cy.get('nav[class="app__nav"] a:contains("Website")').click();
		cy.get('button[id="plugins-button"]').click();
		// Find and enable the plugin
		cy.get('input[id^="select-cell-mostcitedplugin-enabled"]').click();
		cy.get('div:contains(\'The plugin "Most Cited Submissions" has been enabled.\')');
		cy.waitJQuery();
		cy.get('tr[id="component-grid-settings-plugins-settingsplugingrid-category-generic-row-mostcitedplugin"] a[class="show_extras"]').click();
		cy.get('a[id^="component-grid-settings-plugins-settingsplugingrid-category-generic-row-mostcitedplugin-settings-button"]').click();
		// Fill out settings form
		cy.get('form[id="mostCitedSettings"] input[name="mostCitedTitle[en_US]"]').clear().type('Most Cited automated Test Title');
		cy.get('form[id="mostCitedSettings"] input[name="mostCitedTitle[fr_CA]"]').clear().type('Les plus visités');
		cy.get('form[id="mostCitedSettings"] input[name="mostCitedDays"]').clear().type('60');
		cy.get('form[id="mostCitedSettings"] input[name="mostCitedquantity"]').clear().type('10');
		cy.get('form[id="mostCitedSettings"] input[name="mostCitedYears"]').clear().type('25');
		cy.get('form[id="mostCitedSettings"] input[name="mostCitedPosition"]').check();
		// submit settings form
		cy.get('form[id="mostCitedSettings"] button[id^="submitFormButton"]').click();
		cy.waitJQuery();
		cy.get('div:contains(\'Your changes have been saved.\')');
	});

	it('Check Most Cited Content ', function () {
		cy.visit('/');
		cy.get('div[class^="most-cited"]');
		cy.get('h2[class="most-cited-headline"]').should('have.text', 'Most Cited automated Test Title');
	});
});
