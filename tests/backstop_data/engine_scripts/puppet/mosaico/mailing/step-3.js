'use strict';

module.exports = async (engine, scenario, viewport) => {
  await require('./step-1.js') (engine, scenario, viewport);
  // Wait for Angular to attach events
  await engine.waitFor(200);
  await engine.click('.crm_wizard .nav > li:nth-child(3) > a');
  await engine.click('#schedule-send-at');
}
