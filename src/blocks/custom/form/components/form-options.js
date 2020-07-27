import { __ } from '@wordpress/i18n';
import { PanelBody, TextControl, SelectControl, BaseControl } from '@wordpress/components';
import { RichText } from '@wordpress/block-editor';

export const FormOptions = (props) => {
  const {
    attributes: {
      blockClass,
      action,
      method,
      target,
      id,
      classes,
      type,
      dynamicsEntity,
      successMessage,
      errorMessage,
    },
    actions: {
      onChangeAction,
      onChangeMethod,
      onChangeTarget,
      onChangeId,
      onChangeClasses,
      onChangeType,
      onChangeDynamicsEntity,
      onChangeSuccessMessage,
      onChangeErrorMessage,
    },
  } = props;

  console.log({
    successMessage,
    onChangeSuccessMessage,
    errorMessage,
    onChangeErrorMessage,
  });

  const richTextClass = `${blockClass}__rich-text`;

  const formTypes = [
    { label: __('Email', 'eightshift-forms'), value: 'email' },
    { label: __('Custom', 'eightshift-forms'), value: 'custom' },
  ];

  const {
    isDynamicsCrmUsed,
    dynamicsCrm = [],
  } = window.eightshiftForms;

  // All Dynamics CRM config stuff
  let crmEntitiesAsOptions = [];
  if (isDynamicsCrmUsed) {
    crmEntitiesAsOptions = dynamicsCrm.availableEntities.map((entity) => {
      return {
        label: entity,
        value: entity,
      };
    });

    formTypes.push({ label: __('Microsoft Dynamics CRM 365', 'eightshift-forms'), value: 'dynamics-crm' });
  }

  return (
    <PanelBody title={__('Form Settings', 'eightshift-forms')}>
      {onChangeType &&
        <SelectControl
          label={__('Type', 'eightshift-forms')}
          value={type}
          help={__('Choose what will this form do on submit', 'eightshift-forms')}
          options={formTypes}
          onChange={onChangeType}
        />
      }

      {onChangeDynamicsEntity && isDynamicsCrmUsed && type === 'dynamics-crm' &&
        <SelectControl
          label={__('CRM Entity', 'eightshift-forms')}
          help={__('Please enter the name of the entity record to which you wish to add records.', 'eightshift-forms')}
          value={type}
          options={crmEntitiesAsOptions}
          onChange={onChangeDynamicsEntity}
        />
      }

      {onChangeSuccessMessage &&
        <BaseControl
          label={__('Success message', 'eightshift-forms')}
          help={__('Message that the user will see if forms successfully submits.', 'eightshift-forms')}
        >
          <RichText
            className={richTextClass}
            placeholder={__('Add your success message', 'eightshift-forms')}
            onChange={onChangeSuccessMessage}
            value={successMessage}
          />
        </BaseControl>
      }

      {onChangeErrorMessage &&
        <BaseControl
          label={__('Error message', 'eightshift-forms')}
          help={__('Message that the user will see if forms fails to submit for whatever reason.', 'eightshift-forms')}
        >
          <RichText
            className={richTextClass}
            placeholder={__('Add your error message', 'eightshift-forms')}
            onChange={onChangeErrorMessage}
            value={errorMessage}
          />
        </BaseControl>
      }

      {onChangeAction && type === 'custom' &&
        <TextControl
          label={__('Action', 'eightshift-forms')}
          value={action}
          onChange={onChangeAction}
        />
      }

      {onChangeMethod && type === 'custom' &&
        <TextControl
          label={__('Method', 'eightshift-forms')}
          value={method}
          onChange={onChangeMethod}
        />
      }

      {onChangeTarget && type === 'custom' &&
        <TextControl
          label={__('Target', 'eightshift-forms')}
          value={target}
          onChange={onChangeTarget}
        />
      }

      {onChangeClasses &&
        <TextControl
          label={__('Classes', 'eightshift-forms')}
          value={classes}
          onChange={onChangeClasses}
        />
      }

      {onChangeId &&
        <TextControl
          label={__('ID', 'eightshift-forms')}
          value={id}
          onChange={onChangeId}
        />
      }
    </PanelBody>
  );
};
