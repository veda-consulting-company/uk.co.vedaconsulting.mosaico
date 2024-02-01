(function(angular, $, _) {
  angular.module('crmMosaico').service('crmMosaicoVariants', function() {
    // This utility helps you manipulate the `variants` on a mailing.

    function angularObj(obj) {
      return JSON.parse(angular.toJson(obj));
    }

    return {
      getLabels: () => ['A', 'B'],

      // Enable variations for a particular field
      // Ex: crmMosaicoVariants.addVariation(mymailing, 'subject')
      add: function add(mailing, field) {
        mailing.template_options.variants = mailing.template_options.variants || [];
        for (var vid = 0; vid < 2; vid++) {
          mailing.template_options.variants[vid] = mailing.template_options.variants[vid] || {};
          mailing.template_options.variants[vid][field] = mailing[field];
        }
      },

      // Disable variations for a particular field. Remove a particular record.
      // Ex: crmMosaicoVariants.removeVariation(mymailing, 'subject', 1)
      remove: function remove(mailing, field, badVid) {
        delete mailing.template_options.variants[badVid][field];

        // If there's only one value of `field`, then move it to top.
        const remainders = _.filter(mailing.template_options.variants, (v) => v[field] !== undefined);
        if (remainders.length === 1) {
          mailing[field] = remainders[0][field];
          for (var delVid = 0; delVid < 2; delVid++) {
            delete mailing.template_options.variants[delVid][field];
          }
        }

        // If the variants are empty, then delete the variant objects
        const nonEmpties = _.filter(mailing.template_options.variants, (v) => !_.isEmpty(angularObj(v)));
        if (nonEmpties.length === 0) {
          delete mailing.template_options.variants;
        }
      },

      isSplit: function isSplit(mailing, field) {
        return mailing.template_options && mailing.template_options.variants && (field in mailing.template_options.variants[0]);
      }
    };
  });
})(angular, CRM.$, CRM._);
