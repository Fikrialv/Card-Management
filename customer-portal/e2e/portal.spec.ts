import { expect, test } from '@playwright/test'
import AxeBuilder from '@axe-core/playwright'

const viewports = [
  { name: 'small-phone', width: 320, height: 844 },
  { name: 'phone', width: 375, height: 844 },
  { name: 'tablet', width: 768, height: 1024 },
  { name: 'laptop', width: 1024, height: 900 },
  { name: 'desktop', width: 1440, height: 900 },
]

for (const viewport of viewports) {
  test(`${viewport.name}: request and tracking stay usable`, async ({ page }) => {
    await page.setViewportSize(viewport)
    await page.goto('/')
    await expect(page.getByRole('heading', { name: 'Pengajuan kartu RFID' })).toBeVisible()
    await expect(page.getByRole('link', { name: /Unduh template Excel/i })).toBeVisible()
    const metrics = await page.evaluate(() => ({ body: document.body.scrollWidth, viewport: window.innerWidth }))
    expect(metrics.body).toBeLessThanOrEqual(metrics.viewport)
    await page.getByRole('button', { name: /Lacak pengajuan/i }).click()
    await expect(page.getByLabel(/Nomor pengajuan/i)).toBeVisible()
    await expect(page.getByLabel(/Kode pelacakan/i)).toBeVisible()
  })
}

test('keyboard reaches language and primary navigation', async ({ page }) => {
  await page.goto('/')
  await page.keyboard.press('Tab')
  await expect(page.locator(':focus')).toHaveAttribute('href', '#main')
  await page.keyboard.press('Tab')
  await expect(page.locator(':focus')).toHaveText('ID')
  await page.keyboard.press('Tab')
  await expect(page.locator(':focus')).toHaveText('EN')
})

test('portal has no automated WCAG violations on the initial journey', async ({ page }) => {
  await page.goto('/')
  const idResults = await new AxeBuilder({ page }).analyze()
  expect(idResults.violations).toEqual([])
  await page.getByRole('button', { name: 'EN', exact: true }).click()
  const enResults = await new AxeBuilder({ page }).analyze()
  expect(enResults.violations).toEqual([])
})
