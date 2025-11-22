const { test, expect } = require('@playwright/test');

test.describe('Directory Framework Verification', () => {

    test('Homepage loads without errors', async ({ page }) => {
        await page.goto('http://localhost');
        await expect(page).toHaveTitle(/My Awesome Site/);
    });

    test('Company Archive loads', async ({ page }) => {
        await page.goto('http://localhost/companies/');
        // Should not be 404
        const title = await page.title();
        expect(title).not.toContain('Page not found');
    });

    test('Single Company Page loads', async ({ page }) => {
        // Assuming "Acme Corp" was created by demo importer
        await page.goto('http://localhost/companies/acme-corp/');
        const title = await page.title();
        expect(title).not.toContain('Page not found');
        await expect(page.locator('h1')).toContainText('Acme Corp');
    });

    test('Review Form exists on Single Listing', async ({ page }) => {
        await page.goto('http://localhost/companies/acme-corp/');
        await expect(page.locator('.review-form')).toBeVisible();
    });

    test('Location Taxonomy Page loads', async ({ page }) => {
        await page.goto('http://localhost/location/new-york/');
        const title = await page.title();
        expect(title).not.toContain('Page not found');
        await expect(page.locator('h1')).toContainText('Best Companies in New York');
    });

    test('Industry Taxonomy Page loads', async ({ page }) => {
        await page.goto('http://localhost/industry/software/');
        const title = await page.title();
        expect(title).not.toContain('Page not found');
        await expect(page.locator('h1')).toContainText('Top Software Companies');
    });

});
