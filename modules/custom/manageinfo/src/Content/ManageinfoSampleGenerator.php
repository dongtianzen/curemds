<?php
/**
 * @file
 */

namespace Drupal\manageinfo\Content;

use Drupal\Core\Controller\ControllerBase;

use Drupal\manageinfo\Content\ManageinfoJsonGenerator;

/**
 *
 */
class ManageinfoSampleGenerator extends ManageinfoJsonGenerator {

  /**
   *
   */
  public function angularJson() {
    $this->setPostUrl('page/forms/preform/add');
    $this->setRedirectUrl('page/forms/preform/redirect');

    $output['formInfo'] = array(
      'postUrl' =>  $this->getPostUrl(),
      'redirectUrl' => $this->getRedirectUrl(),
    );

    $form_elements[] = $this->getTextfield('eventRegionName', 'Quesiton 1', array('defaultValue' => 'canada', 'fieldRequired' => TRUE));
    // $form_elements[] = $this->getSlider('eventRegionName', 'Confidence in Project', array('minimumStep' => 1, 'minimumValue' => 0, 'maximumValue' => 10));
    $form_elements[] = $this->getTextfield('eventRegionName', 'Quesiton 2');
    // $form_elements[] = $this->getTextfield('eventRegionName', 'City', array('defaultValue' => 'Windsor'));
    // $form_elements[] = $this->getTextfield('eventRegionName', 'Province', array('defaultValue' => 'Ontario'));
    // $form_elements[] = $this->getTextfield('eventRegionName', 'Location', array('defaultValue' => 'Randolph'));
    // $form_elements[] = $this->getDateTime('field_dateTime_abbrname', 'Select Time1', array('defaultValue' => 1522055700, 'fieldTid' => 1109));
    // $form_elements[] = $this->getDateTime('field_dateTime_abbrname', 'Select Time2', array('fieldTid' => 1123));

    $gender_options = array(
      array(
        "termTid" => 1,
        "termName" => "Male",
      ),
      array(
        "termTid" => 2,
        "termName" => "Female",
      ),
    );
    $form_elements[] = $this->getSelect('eventRegionName', 'Gender', array('fieldLabel' => $gender_options));

    $working_daignosis_options = array(
      array(
        "termTid" => 101,
        "termName" => "A.Idiopathic intersririal Pneumonia",
      ),
      array(
        "termTid" => 102,
        "termName" => "B.(CTD)-ILD",
      ),
       array(
        "termTid" => 103,
        "termName" => "C.Other",
      ),
    );
    $a_options = array(
      array(
        "termTid" => 9001,
        "termName" => "A1",
      ),
      array(
        "termTid" => 9002,
        "termName" => "A2",
      ),
       array(
        "termTid" => 9003,
        "termName" => "A3",
      ),
    );
    $b_options = array(
      array(
        "termTid" => 6701,
        "termName" => "B1",
      ),
      array(
        "termTid" => 6702,
        "termName" => "B2",
      ),
       array(
        "termTid" => 6703,
        "termName" => "B3",
      ),
    );
    $form_elements[] = $this->getSelect('field_singleSelectfather_one_abbrname', 'Working Diagnosis', array('fieldLabel' => $working_daignosis_options,
      'fieldCategory' => 'hierarchyFather'));
    $form_elements[] = $this->getMultiSelect('field_singleSelectfathermultiplechild_first_abbrname', 'Select the following a:', array('fieldLabel' => $a_options, 'parentTermTid'=> 101, 'parentFieldName' => 'field_singleSelectfather_one_abbrname', "fieldShow" => FALSE));
    $form_elements[] = $this->getMultiSelect('field_singleSelectfathermultiplechild_second_abbrname', 'Select the following b:', array('fieldLabel' => $b_options, 'parentTermTid'=> 102, 'parentFieldName' => 'field_singleSelectfather_one_abbrname', "fieldShow" => FALSE));

    $working_ideas_options = array(
      array(
        "termTid" => 441,
        "termName" => "WAA",
      ),
      array(
        "termTid" => 442,
        "termName" => "WAB",
      ),
       array(
        "termTid" => 443,
        "termName" => "WAC",
      ),
    );
    $c_options = array(
      array(
        "termTid" => 4501,
        "termName" => "WA1",
      ),
      array(
        "termTid" => 4502,
        "termName" => "WA2",
      ),
       array(
        "termTid" => 4503,
        "termName" => "WA3",
      ),
    );

    $form_elements[] = $this->getSelect('field_singleSelectfather_two_abbrname', 'Working Ideas', array('fieldLabel' => $working_ideas_options, 'fieldCategory' => 'hierarchyFather'));
    $form_elements[] = $this->getMultiSelect('field_singleSelectfathermultiplechild_three_abbrname', 'Select the following 2:', array('fieldLabel' => $c_options, 'parentTermTid'=> 443, 'parentFieldName' => 'field_singleSelectfather_two_abbrname', "fieldShow" => FALSE));

    $multi_select_father_options = array(
      array(
        "termTid" => 2123,
        "termName" => "Autoimmune Serology",
      ),
      array(
        "termTid" => 2124,
        "termName" => "Bronchoscopy",
      ),
      array(
        "termTid" => 2125,
        "termName" => "CT Chest",
      ),
      array(
       "termTid" => 2126,
       "termName" => "Lung biopsy(VATS)",
      ),
    );
    $form_elements[] = $this->getMultiSelect('field_singleSelectFatherSpecificAnswer_abbrname', 'Investigations Performed to Date:', array('fieldLabel' => $multi_select_father_options, 'fieldCategory' => 'specificAnswer'));

    $multi_select_child_options = array(
      array(
        "termTid" => 4143,
        "termName" => "Definite UIP",
      ),
      array(
        "termTid" => 4144,
        "termName" => "Possible UIP",
      ),
      array(
        "termTid" => 4145,
        "termName" => "Inconsistent with UIP",
      ),
      array(
       "termTid" => 4146,
       "termName" => "Other",
      ),
    );
    $form_elements[] = $this->getSelect('field_singleSelectFatherSpecificAnswerChild_abbrname', 'What is the current interpretation of the CT Chest?', array('fieldLabel' => $multi_select_child_options, 'parentFieldName' => 'field_singleSelectFatherSpecificAnswer_abbrname', "fieldShow" => FALSE, 'fieldClass' => 'margin-top-40'));


    $select_filter_father_options = array(
      array(
        "termTid" => 1112,
        "termName" => "R1",
      ),
      array(
        "termTid" => 1123,
        "termName" => "R2",
      ),
      array(
        "termTid" => 2233,
        "termName" => "R3",
      ),
    );
    $form_elements[] = $this->getSelect('field_singleSelectfather_one_abbrname', 'Select me to filter my child', array('fieldLabel' => $select_filter_father_options, 'fieldCategory' => 'filterFather'));

    $select_filter_child_options = array(
      array(
        "termTid" => 56589,
        "termName" => "R1 first child",
        "termParentTid" => 1112,
      ),
      array(
        "termTid" => 55345,
        "termName" => "R1 second child",
        "termParentTid" => 1112,

      ),
      array(
        "termTid" => 55421,
        "termName" => "R2 first child",
        "termParentTid" => 1123,
      ),
      array(
        "termTid" => 55421,
        "termName" => "R2 second child",
        "termParentTid" => 1123,
      ),
      array(
        "termTid" => 55456,
        "termName" => "R3 first child",
        "termParentTid" => 2233,
      ),
    );
    $form_elements[] = $this->getSelect('field_singleSelectfathermultiplechild_one_abbrname', 'Select Child', array('fieldLabel' => $select_filter_child_options, 'parentFieldName' => 'field_singleSelectfather_one_abbrname', 'fieldLabelOptions' => 'filteredChildren', 'fieldShow' => FALSE));

    $select_filter_father_options2 = array(
      array(
        "termTid" => 6662,
        "termName" => "A1",
      ),
      array(
        "termTid" => 6663,
        "termName" => "A2",
      ),
      array(
        "termTid" => 6664,
        "termName" => "A3",
      ),
    );
    $form_elements[] = $this->getSelect('field_singleSelectfather_two_abbrname', 'Select me to filter my child 2', array('fieldLabel' => $select_filter_father_options2, 'fieldTid' => 556677, 'fieldCategory' => 'filterFather'));

    $select_filter_child_options2 = array(
      array(
        "termTid" => 11789,
        "termName" => "A1 first child",
        "termParentTid" => 6662,
      ),
      array(
        "termTid" => 11345,
        "termName" => "A1 second child",
        "termParentTid" => 6662,

      ),
      array(
        "termTid" => 33421,
        "termName" => "A2 first child",
        "termParentTid" => 6663,
      ),
      array(
        "termTid" => 33421,
        "termName" => "A2 second child",
        "termParentTid" => 6663,
      ),
      array(
        "termTid" => 22456,
        "termName" => "A3 first child",
        "termParentTid" => 6664,
      ),
    );
    $form_elements[] = $this->getSelect('field_singleSelectfathermultiplechild_two_abbrname', 'Select Child for 2', array('fieldLabel' => $select_filter_child_options2, 'parentFieldName' => 'field_singleSelectfather_two_abbrname', 'fieldLabelOptions' => 'filteredChildren', 'fieldShow' => FALSE));

    $business_units =  array(
      array(
        "termTid" => 66789,
        "termName" => "BU1",
      ),
      array(
        "termTid" => 66345,
        "termName" => "BU2",

      ),
      array(
        "termTid" => 66421,
        "termName" => "BU3",
      ),
    );
    $form_elements[] = $this->getSelect('field_business_unit_abbrname', 'Select BUsiness Unit', array('fieldLabel' => $business_units,
      'fieldCategory' => 'filterFather'));

    $therapeutic_areas = array(
      array(
        "termTid" => 55789,
        "termName" => "B1TA1",
        "termParentTid" => array(66789, 66345),
      ),
      array(
        "termTid" => 55345,
        "termName" => "B1TA2",
        "termParentTid" => array(66789),
      ),
      array(
        "termTid" => 3352421,
        "termName" => "B2TA1",
        "termParentTid" => array(66345),
      ),
      array(
        "termTid" => 543224,
        "termName" => "B2TA2",
        "termParentTid" => array(66345),
      ),
      array(
        "termTid" => 81456,
        "termName" => "B3TA1",
        "termParentTid" => array(66421),
      ),
    );
    $form_elements[] = $this->getSelect('field_therapeutic_area_abbrname', 'Select Therapeutic Area', array('fieldLabel' => $therapeutic_areas,  'parentFieldName' => 'field_business_unit_abbrname', 'fieldShow' => TRUE, 'fieldLabelOptions' => 'filteredChildren', 'fieldCategory' => 'filterFather'));

    $programs = array(
      array(
        "termTid" => 91828789,
        "termName" => "B1TA1P1",
        "termParentTid" => 55789,
      ),
      array(
        "termTid" => 91828345,
        "termName" => "B1TA1P1",
        "termParentTid" => 55789,

      ),
      array(
        "termTid" => 8961252421,
        "termName" => "B2TA1P1",
        "termParentTid" => 3352421,
      ),
      array(
        "termTid" => 7989024,
        "termName" => "B2TA2P1",
        "termParentTid" => 543224,
      ),
      array(
        "termTid" => 5768756,
        "termName" => "B3TA1P1",
        "termParentTid" => 81456,
      ),
    );
    $form_elements[] = $this->getSelect('field_programs_abbrname', 'Select PROGRAMS', array('fieldLabel' => $programs, 'parentFieldName' => 'field_therapeutic_area_abbrname', 'fieldShow' => FALSE, 'fieldLabelOptions' => 'filteredChildren'));

    $form_elements[] = $this->getCheckbox('eventName', 'Test Checkbox');

    $output['formElementsSection'] = $form_elements;

    return $output;
  }

}
