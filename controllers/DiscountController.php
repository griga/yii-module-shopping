<?php
/** Created by griga at 20.11.13 | 15:28.
 * 
 */

class DiscountController extends BackEndController {
    /**
     *
     */
    public function actionIndex()
    {
        $model = new Discount('search');
        $model->unsetAttributes();
        if(isset($_GET['Discount']))
            $model->attributes = $_GET['Discount'];
        $this->render('index', array(
        	'model' => $model,
        ));
    }

    public function actionCreate()
    {
        $model = new Discount();
        if(r()->isAjaxRequest){
            header('Cache-Control: no-cache, must-revalidate');
            header('Content-type: application/json');

            if (isset($_POST['Discount'])) {
                $model->attributes = $_POST['Discount'];
                if(isset($_GET['user_id']))
                    $model->user_id = $_GET['user_id'];
                if($model->save())
                    echo CJavaScript::jsonEncode(array(
                        'id'=>$model->id,
                        'discount'=>$model->toString(),

                    ));
                else{
                    echo CJavaScript::jsonEncode(array('errors'=>$model->errors));
                }
                Yii::app()->end();
            }
            $this->renderPartial('form', array(
                'model' => $model,
            ));
            Yii::app()->end();
        } else {
            if (isset($_POST['Discount'])) {
                $model->attributes = $_POST['Discount'];
                if(!$model->user_id)
                    $model->user_id = null;
                if($model->save())
                    $this->redirect('/backend/shopping/discount/index');
            }
            $this->render('form', array(
                'model' => $model,
            ));
        }

    }

    public function actionUpdate($id)
    {
        /** @var Discount $model */
        $model = Discount::model()->findByPk($id);

        if(r()->isAjaxRequest){
            header('Cache-Control: no-cache, must-revalidate');
            header('Content-type: application/json');

            if (isset($_POST['Discount'])) {
                $model->attributes = $_POST['Discount'];
                if($model->save())
                    echo CJavaScript::jsonEncode(array(
                        'id'=>$model->id,
                        'discount'=>$model->toString(),
                    ));
                else{
                    echo CJavaScript::jsonEncode(array('errors'=>$model->errors));
                }
                Yii::app()->end();
            }
            $this->renderPartial('form', array(
                'model' => $model,
            ));
            Yii::app()->end();
        } else {
            if (isset($_POST['Discount'])) {
                $model->attributes = $_POST['Discount'];
                if($model->save())
                    $this->redirect('/backend/shopping/discount/index');
            }
            $this->render('form', array(
                'model' => $model,
            ));
        }
    }

    /**
     *
     */
    public function actionDelete($id)
    {
        Discount::model()->deleteByPk($id);
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }

} 