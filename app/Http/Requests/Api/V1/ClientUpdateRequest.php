<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiTrait;
use Illuminate\Validation\Rule;
use App\Rules\SaudiMobileRule;
use App\Traits\NormalizesSaudiMobile;
class ClientUpdateRequest extends FormRequest
{
    use ApiTrait, NormalizesSaudiMobile;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
       $userId = $this->user()->id;

      return [
          'name'   => 'required|max:100',
         'mobile' => [
             'required',
              SaudiMobileRule::make(),
              Rule::unique('users', 'mobile')
                  ->whereNull('deleted_at')
                  ->ignore($userId),
          ],
          'email'  => [
              'nullable',
              'email',
              'max:255',
              Rule::unique('users', 'email')
                  ->whereNull('deleted_at')
                  ->ignore($userId),
          ],
          'image'  => 'nullable|image',
      ];
    }

    public function failedValidation(Validator $validator)

    {

        throw new HttpResponseException(
            $this->returnError(
                $validator->errors()->first()
            )
        );
    }
    public function attributes()
    {
        return [
            'name' => __('dashboard.Name'),
            'mobile' => __('dashboard.Mobile'),
            'email' => __('dashboard.Email'),
            'image' => __('dashboard.Image'),

        ];
    }
}
